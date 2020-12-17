<?php

namespace App\Models;

use CodeIgniter\Model;

class ResepModel extends Model
{
    protected $table = 'komik';
    protected $useTimestamps = true;
    protected $allowedFields = ['judul', 'slug', 'penulis',  'sampul'];

    public function getKomik($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }

        return $this->where(['slug' => $slug])->first();
    }
}