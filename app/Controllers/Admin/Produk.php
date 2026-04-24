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
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $kategori_id = $this->request->getGet('kategori_id');
        $supplier_id = $this->request->getGet('supplier_id');
        $status_stok = $this->request->getGet('status_stok');
        $perPage = 10;
        
        $builder = $this->produkModel
            ->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left');
        
        // Filter pencarian
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('produk.nama_barang', $keyword)
                ->orLike('produk.sku', $keyword)
                ->groupEnd();
        }
        
        // Filter kategori
        if (!empty($kategori_id)) {
            $builder->where('produk.id_kategori', $kategori_id);
        }
        
        // Filter supplier
        if (!empty($supplier_id)) {
            $builder->where('produk.id_supplier', $supplier_id);
        }
        
        // Filter status stok
        if ($status_stok == 'menipis') {
            $builder->where('produk.stok <=', 'produk.min_stok', false);
        } elseif ($status_stok == 'habis') {
            $builder->where('produk.stok', 0);
        } elseif ($status_stok == 'aman') {
            $builder->where('produk.stok >', 'produk.min_stok', false);
        }
        
        $produk = $builder->orderBy('produk.id', 'DESC')->paginate($perPage);
        $pager = $this->produkModel->pager;
        
        $data = [
            'title' => 'Kelola Master Produk',
            'produk' => $produk,
            'kategori' => $this->kategoriModel->findAll(),
            'supplier' => $this->supplierModel->findAll(),
            'pager' => $pager,
            'keyword' => $keyword,
            'kategori_id' => $kategori_id,
            'supplier_id' => $supplier_id,
            'status_stok' => $status_stok
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
        // Validasi
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
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Harga jual wajib diisi.',
                    'numeric' => 'Harga jual harus berupa angka.',
                    'greater_than' => 'Harga jual harus lebih dari 0.'
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

        // Validasi manual harga_jual > harga_beli
        $harga_beli = (float) $this->request->getPost('harga_beli');
        $harga_jual = (float) $this->request->getPost('harga_jual');

        if ($harga_jual <= $harga_beli) {
            return redirect()->back()->withInput()->with('errors', ['harga_jual' => 'Harga jual harus lebih besar dari harga beli.']);
        }

        try {
            $data = [
                'sku' => $this->request->getPost('sku'),
                'nama_barang' => $this->request->getPost('nama_barang'),
                'id_kategori' => $this->request->getPost('id_kategori'),
                'id_supplier' => $this->request->getPost('id_supplier'),
                'harga_beli' => $harga_beli,
                'harga_jual' => $harga_jual,
                'stok' => $this->request->getPost('stok'),
                'min_stok' => $this->request->getPost('min_stok') ?? 0,
                'keterangan' => $this->request->getPost('keterangan'),
            ];

            // Insert langsung tanpa transaksi kompleks
            $insertId = $this->produkModel->insert($data);
            
            if ($insertId) {
                return redirect()->to('/admin/produk')->with('success', 'Produk berhasil disimpan.');
            } else {
                $errors = $this->produkModel->errors();
                log_message('error', 'Insert produk gagal: ' . json_encode($errors));
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . json_encode($errors));
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }

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
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Harga jual wajib diisi.',
                    'numeric' => 'Harga jual harus berupa angka.',
                    'greater_than' => 'Harga jual harus lebih dari 0.'
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

        // Validasi manual harga_jual > harga_beli
        $harga_beli = (float) $this->request->getPost('harga_beli');
        $harga_jual = (float) $this->request->getPost('harga_jual');

        if ($harga_jual <= $harga_beli) {
            return redirect()->back()->withInput()->with('errors', ['harga_jual' => 'Harga jual harus lebih besar dari harga beli.']);
        }

        $dataUpdate = [
            'sku' => $this->request->getPost('sku'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'id_supplier' => $this->request->getPost('id_supplier'),
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'min_stok' => $this->request->getPost('min_stok') ?? 0,
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        try {
            if ($this->produkModel->update($id, $dataUpdate)) {
                return redirect()->to('/admin/produk')->with('success', 'Data produk berhasil diperbarui.');
            } else {
                $errors = $this->produkModel->errors();
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui: ' . json_encode($errors));
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        try {
            $this->produkModel->delete($id);
            return redirect()->back()->with('success', 'Produk telah dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}