<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table      = 'mahasiswa';
    protected $primaryKey = 'id';

    protected $allowedFields = ['nama', 'prodi'];

    protected $useTimestamps = false;
    protected $returnType    = 'array';
}
