<?php

namespace App\Controllers;

use App\Models\ResepModel;


class Resep extends BaseController
{
    protected $resepModel;

    public function __construct()
    {
        $this->resepModel = new ResepModel();
    }

    public function index()
    {
        // $komik = $this->resepModel->findAll();

        $data = [
            'title' => 'Daftar Resep',
            'resep' => $this->resepModel->getResep()
        ];

        // $komikModel = new KomikModel();




        return view('resep/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Resep',
            'resep' => $this->resepModel->getResep($slug)
        ];

        // jika komik tidak ada di tabel
        if (empty($data['resep'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul resep ' . $slug . ' tidak ditemukan.');
        }
        return view('resep/detail', $data);
    }

    public function create()
    {
        // session();
        $data = [
            'title' => 'FormTambah Data Resep',
            'validation' => \Config\Services::validation()
        ];

        return view('resep/create', $data);
    }

    public function save()
    {

        // validasi input
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[resep.judul]',
                'errors' => [
                    'required' => '{field} resep harus di isi.',
                    'is_unique' => '{field} resep sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'

                ]
            ]
        ])) {
            // $validation = \Config\Services::validation();
            // return redirect()->to('/Komik/create')->withInput()->with('validation', $validation);
            return redirect()->to('/Resep/create')->withInput();
        }

        // ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        // apakah tidak ada gambar yg di upload
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.jpg';
        } else {
            // generate nama sampul random
            $namaSampul = $fileSampul->getRandomName();
            // pindahkan file ke folder img
            $fileSampul->move('img', $namaSampul);
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),

            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil di tambahkan.');
        return redirect()->to('/resep');
    }

    public function delete($id)
    {
        // cari gambar berdasarkan id
        $resep = $this->resepModel->find($id);

        // cek jika file gambarnya default
        if ($resep['sampul'] != 'default.jpg') {
            // hapus gambar
            unlink('img/' . $resep['sampul']);
        }




        $this->resepkModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil di hapus.');
        return redirect()->to('/resep');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Ubah Data Komik',
            'validation' => \Config\Services::validation(),
            'resep' => $this->resepModel->getResep($slug)
        ];

        return view('resep/edit', $data);
    }

    public function update($id)
    {
        // cek judul
        $komikLama = $this->resepModel->getResep($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} resep harus di isi.',
                    'is_unique' => '{field} resep sudah terdaftar'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'

                ]
            ]
        ])) {
            return redirect()->to('/Resep/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $fileSampul = $this->request->getFile('sampul');

        // cek gambar apakah tetap gambar lama
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            // generate nama file random
            $namaSampul = $fileSampul->getRandomName();
            // pindahkan gambar
            $fileSampul->move('img', $namaSampul);
            // hapus file sampul
            unlink('img/' . $this->request->getVar('sampulLama'));
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),

            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil di ubah.');

        return redirect()->to('/resep');
    }
}
