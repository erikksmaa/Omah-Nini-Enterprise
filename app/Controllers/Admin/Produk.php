<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;

class Produk extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new SupplierModel();
    }

  public function index()
    {
        // Gunakan paginate(10) untuk 10 data per halaman
        $produk = $this->produkModel->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left')
            ->orderBy('produk.id', 'DESC')
            ->paginate(10); // 10 data per halaman
        
        $data = [
            'title' => 'Kelola Master Produk',
            'produk' => $produk,
            'kategori' => $this->kategoriModel->findAll(),
            'supplier' => $this->supplierModel->findAll(),
            'pager' => $this->produkModel->pager
        ];
        
        return view('admin/produk/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Produk Baru',
            'kategori' => $this->kategoriModel->findAll(),
            'supplier' => $this->supplierModel->findAll()
        ];
        return view('admin/produk/create', $data);
    }

    public function store()
    {
        // VALIDASI LENGKAP
        $rules = [
            'sku' => [
                'rules' => 'required|is_unique[produk.sku]|min_length[4]|max_length[50]|alpha_numeric_punct',
                'errors' => [
                    'required' => 'SKU wajib diisi.',
                    'is_unique' => 'SKU sudah ada. Gunakan SKU yang berbeda.',
                    'min_length' => 'SKU minimal 4 karakter.',
                    'max_length' => 'SKU maksimal 50 karakter.',
                    'alpha_numeric_punct' => 'SKU hanya boleh berisi huruf, angka, dan tanda hubung/garis bawah.'
                ]
            ],
            'nama_barang' => [
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'Nama barang wajib diisi.',
                    'min_length' => 'Nama barang minimal 3 karakter.',
                    'max_length' => 'Nama barang maksimal 200 karakter.'
                ]
            ],
            'id_kategori' => [
                'rules' => 'required|is_natural_no_zero|is_not_unique[kategori.id]',
                'errors' => [
                    'required' => 'Kategori wajib dipilih.',
                    'is_natural_no_zero' => 'Kategori tidak valid.',
                    'is_not_unique' => 'Kategori yang dipilih tidak ditemukan.'
                ]
            ],
            'id_supplier' => [
                'rules' => 'required|is_natural_no_zero|is_not_unique[supplier.id]',
                'errors' => [
                    'required' => 'Supplier wajib dipilih.',
                    'is_natural_no_zero' => 'Supplier tidak valid.',
                    'is_not_unique' => 'Supplier yang dipilih tidak ditemukan.'
                ]
            ],
            'harga_beli' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Harga beli wajib diisi.',
                    'numeric' => 'Harga beli harus berupa angka.',
                    'greater_than' => 'Harga beli harus lebih dari 0.'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than[harga_beli]',
                'errors' => [
                    'required' => 'Harga jual wajib diisi.',
                    'numeric' => 'Harga jual harus berupa angka.',
                    'greater_than' => 'Harga jual harus lebih besar dari harga beli.'
                ]
            ],
            'stok' => [
                'rules' => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Stok wajib diisi.',
                    'numeric' => 'Stok harus berupa angka.',
                    'greater_than_equal_to' => 'Stok tidak boleh negatif.'
                ]
            ],
            'min_stok' => [
                'rules' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[stok]',
                'errors' => [
                    'numeric' => 'Minimal stok harus berupa angka.',
                    'greater_than_equal_to' => 'Minimal stok tidak boleh negatif.',
                    'less_than_equal_to' => 'Minimal stok tidak boleh melebihi stok saat ini.'
                ]
            ],
            'keterangan' => [
                'rules' => 'permit_empty|max_length[1000]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 1000 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            // Generate barcode EAN-13 jika tidak diisi
            $barcode = $this->request->getPost('barcode');
            if (empty($barcode)) {
                $barcode = $this->generateBarcodeEAN13();
            }

            $this->produkModel->save([
                'sku' => $this->request->getPost('sku'),
                'nama_barang' => $this->request->getPost('nama_barang'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'harga_beli' => $this->request->getPost('harga_beli'),
                'harga_jual' => $this->request->getPost('harga_jual'),
                'stok' => $this->request->getPost('stok'),
                'min_stok' => $this->request->getPost('min_stok') ?? 0,
                'barcode' => $barcode,
                'keterangan' => $this->request->getPost('keterangan'),
            ]);
            return redirect()->to('/admin/produk')->with('success', 'Produk berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Cek apakah produk ada
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }

        // VALIDASI UPDATE (dengan is_unique exception untuk current ID)
        $rules = [
            'sku' => [
                'rules' => "required|min_length[4]|max_length[50]|alpha_numeric_punct|is_unique[produk.sku,id,{$id}]",
                'errors' => [
                    'required' => 'SKU wajib diisi.',
                    'min_length' => 'SKU minimal 4 karakter.',
                    'max_length' => 'SKU maksimal 50 karakter.',
                    'alpha_numeric_punct' => 'SKU hanya boleh berisi huruf, angka, dan tanda hubung/garis bawah.',
                    'is_unique' => 'SKU sudah digunakan produk lain.'
                ]
            ],
            'nama_barang' => [
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'Nama barang wajib diisi.',
                    'min_length' => 'Nama barang minimal 3 karakter.',
                    'max_length' => 'Nama barang maksimal 200 karakter.'
                ]
            ],
            'id_kategori' => [
                'rules' => 'required|is_natural_no_zero|is_not_unique[kategori.id]',
                'errors' => [
                    'required' => 'Kategori wajib dipilih.',
                    'is_natural_no_zero' => 'Kategori tidak valid.',
                    'is_not_unique' => 'Kategori yang dipilih tidak ditemukan.'
                ]
            ],
            'id_supplier' => [
                'rules' => 'required|is_natural_no_zero|is_not_unique[supplier.id]',
                'errors' => [
                    'required' => 'Supplier wajib dipilih.',
                    'is_natural_no_zero' => 'Supplier tidak valid.',
                    'is_not_unique' => 'Supplier yang dipilih tidak ditemukan.'
                ]
            ],
            'harga_beli' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Harga beli wajib diisi.',
                    'numeric' => 'Harga beli harus berupa angka.',
                    'greater_than' => 'Harga beli harus lebih dari 0.'
                ]
            ],
            'harga_jual' => [
                'rules' => 'required|numeric|greater_than[harga_beli]',
                'errors' => [
                    'required' => 'Harga jual wajib diisi.',
                    'numeric' => 'Harga jual harus berupa angka.',
                    'greater_than' => 'Harga jual harus lebih besar dari harga beli.'
                ]
            ],
            'min_stok' => [
                'rules' => 'permit_empty|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'numeric' => 'Minimal stok harus berupa angka.',
                    'greater_than_equal_to' => 'Minimal stok tidak boleh negatif.'
                ]
            ],
            'keterangan' => [
                'rules' => 'permit_empty|max_length[1000]',
                'errors' => [
                    'max_length' => 'Keterangan maksimal 1000 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'sku' => $this->request->getPost('sku'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'harga_beli' => $this->request->getPost('harga_beli'),
            'harga_jual' => $this->request->getPost('harga_jual'),
            'min_stok' => $this->request->getPost('min_stok') ?? 0,
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        try {
            if ($this->produkModel->update($id, $dataUpdate)) {
                return redirect()->to('/admin/produk')->with('success', 'Data produk berhasil diperbarui.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        // Cek apakah produk ada
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Cek apakah produk terkait dengan transaksi (jika ada)
        // $transaksiModel = new \App\Models\TransaksiModel();
        // $terkait = $transaksiModel->where('id_produk', $id)->countAllResults();
        // if ($terkait > 0) {
        //     return redirect()->back()->with('error', "Produk tidak bisa dihapus karena sudah ada dalam transaksi.");
        // }

        try {
            $this->produkModel->delete($id);
            return redirect()->back()->with('success', 'Produk telah dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Generate barcode EAN-13 otomatis
     */
    private function generateBarcodeEAN13()
    {
        // Prefix 200 untuk internal (bisa disesuaikan)
        $prefix = '200';
        
        // Cari ID terakhir
        $lastId = $this->produkModel->selectMax('id')->get()->getRow()->id ?? 0;
        $nextId = $lastId + 1;
        
        // Generate 12 digit pertama
        $code12 = $prefix . str_pad($nextId, 9, '0', STR_PAD_LEFT);
        $code12 = substr($code12, 0, 12);
        
        // Hitung check digit
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $weight = ($i % 2 == 0) ? 1 : 3;
            $sum += (int)$code12[$i] * $weight;
        }
        $checkDigit = (10 - ($sum % 10)) % 10;
        
        return $code12 . $checkDigit;
    }
}