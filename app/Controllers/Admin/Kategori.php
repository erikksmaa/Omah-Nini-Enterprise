<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController {
    protected $kategoriModel;

    public function __construct() {
        $this->kategoriModel = new KategoriModel();
    }

     public function index()
    {
        // Gunakan paginate(10) untuk 10 data per halaman
        $kategori = $this->kategoriModel->orderBy('id', 'DESC')->paginate(10);
        
        $data = [
            'title' => 'Kelola Kategori Barang',
            'kategori' => $kategori,
            'pager' => $this->kategoriModel->pager
        ];
        
        return view('admin/kategori/index', $data);
    }

    public function store() {
        // VALIDASI LENGKAP
        $rules = [
            'nama' => [
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama]',
                'errors' => [
                    'required' => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'max_length' => 'Nama kategori maksimal 100 karakter.',
                    'is_unique' => 'Nama kategori sudah ada. Silakan gunakan nama lain.'
                ]
            ],
            'deskripsi' => [
                'rules' => 'permit_empty|max_length[500]',
                'errors' => [
                    'max_length' => 'Deskripsi maksimal 500 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->kategoriModel->save([
                'nama'      => $this->request->getPost('nama'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ]);
            return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function update($id) {
        // Validasi update
        $rules = [
            'nama' => [
                'rules' => "required|min_length[3]|max_length[100]|is_unique[kategori.nama,id,{$id}]",
                'errors' => [
                    'required' => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'max_length' => 'Nama kategori maksimal 100 karakter.',
                    'is_unique' => 'Nama kategori sudah digunakan kategori lain.'
                ]
            ],
            'deskripsi' => [
                'rules' => 'permit_empty|max_length[500]',
                'errors' => [
                    'max_length' => 'Deskripsi maksimal 500 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kategoriLama = $this->kategoriModel->find($id);
        if (!$kategoriLama) {
            return redirect()->back()->with('error', 'Data kategori tidak ditemukan.');
        }

        try {
            $this->kategoriModel->update($id, [
                'nama'      => $this->request->getPost('nama'),
                'deskripsi' => $this->request->getPost('deskripsi')
            ]);
            return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        // Cek apakah kategori memiliki produk terkait
        $produkModel = new \App\Models\ProdukModel();
        $produkTerkait = $produkModel->where('id_kategori', $id)->countAllResults();
        
        if ($produkTerkait > 0) {
            return redirect()->back()->with('error', "Kategori tidak bisa dihapus karena masih digunakan oleh {$produkTerkait} produk.");
        }

        try {
            $this->kategoriModel->delete($id);
            return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}