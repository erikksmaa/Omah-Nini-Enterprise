<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\SupplierModel;

class Supplier extends BaseController {
    protected $supplierModel;

    public function __construct() {
        $this->supplierModel = new SupplierModel();
    }

    public function index() {
        $data = [
            'title'    => 'Kelola Data Supplier',
            'supplier' => $this->supplierModel->findAll()
        ];
        return view('admin/supplier/index', $data);
    }

    public function store() {
        $rules = [
            'nama' => [
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[supplier.nama]',
                'errors' => [
                    'required' => 'Nama supplier wajib diisi.',
                    'min_length' => 'Nama supplier minimal 3 karakter.',
                    'max_length' => 'Nama supplier maksimal 100 karakter.',
                    'is_unique' => 'Nama supplier sudah terdaftar.'
                ]
            ],
            'kontak' => [
                'rules' => 'required|min_length[10]|max_length[15]|numeric',
                'errors' => [
                    'required' => 'Nomor kontak wajib diisi.',
                    'min_length' => 'Nomor kontak minimal 10 digit.',
                    'max_length' => 'Nomor kontak maksimal 15 digit.',
                    'numeric' => 'Nomor kontak harus berupa angka.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[supplier.email]',
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email sudah terdaftar.'
                ]
            ],
            'alamat' => [
                'rules' => 'required|min_length[10]|max_length[500]',
                'errors' => [
                    'required' => 'Alamat wajib diisi.',
                    'min_length' => 'Alamat minimal 10 karakter.',
                    'max_length' => 'Alamat maksimal 500 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->supplierModel->save([
                'nama'   => $this->request->getPost('nama'),
                'kontak' => $this->request->getPost('kontak'),
                'email'  => $this->request->getPost('email'),
                'alamat' => $this->request->getPost('alamat'),
            ]);
            return redirect()->back()->with('success', 'Supplier berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function update($id) {
        $rules = [
            'nama' => [
                'rules' => "required|min_length[3]|max_length[100]|is_unique[supplier.nama,id,{$id}]",
                'errors' => [
                    'required' => 'Nama supplier wajib diisi.',
                    'min_length' => 'Nama supplier minimal 3 karakter.',
                    'max_length' => 'Nama supplier maksimal 100 karakter.',
                    'is_unique' => 'Nama supplier sudah digunakan supplier lain.'
                ]
            ],
            'kontak' => [
                'rules' => 'required|min_length[10]|max_length[15]|numeric',
                'errors' => [
                    'required' => 'Nomor kontak wajib diisi.',
                    'min_length' => 'Nomor kontak minimal 10 digit.',
                    'max_length' => 'Nomor kontak maksimal 15 digit.',
                    'numeric' => 'Nomor kontak harus berupa angka.'
                ]
            ],
            'email' => [
                'rules' => "required|valid_email|is_unique[supplier.email,id,{$id}]",
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email sudah digunakan supplier lain.'
                ]
            ],
            'alamat' => [
                'rules' => 'required|min_length[10]|max_length[500]',
                'errors' => [
                    'required' => 'Alamat wajib diisi.',
                    'min_length' => 'Alamat minimal 10 karakter.',
                    'max_length' => 'Alamat maksimal 500 karakter.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->supplierModel->update($id, [
                'nama'   => $this->request->getPost('nama'),
                'kontak' => $this->request->getPost('kontak'),
                'email'  => $this->request->getPost('email'),
                'alamat' => $this->request->getPost('alamat'),
            ]);
            return redirect()->back()->with('success', 'Data supplier diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        // Cek apakah supplier memiliki produk terkait
        $produkModel = new \App\Models\ProdukModel();
        $produkTerkait = $produkModel->where('id_supplier', $id)->countAllResults();
        
        if ($produkTerkait > 0) {
            return redirect()->back()->with('error', "Supplier tidak bisa dihapus karena masih memasok {$produkTerkait} produk.");
        }

        try {
            $this->supplierModel->delete($id);
            return redirect()->back()->with('success', 'Supplier telah dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}