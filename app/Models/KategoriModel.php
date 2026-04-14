<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
   protected $table            = 'kategori';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama', 'deskripsi']; 
    
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // Kategori jarang diupdate
}
