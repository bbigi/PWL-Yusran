<?php

namespace App\Controllers;

use App\Models\MahasiswaModel;

class Mahasiswa extends BaseController
{
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->mahasiswaModel = new MahasiswaModel();
    }

    public function index()
    {
        return view('Mahasiswa_view');
    }

    public function getData()
    {
        $data = $this->mahasiswaModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function simpan()
    {
        $this->mahasiswaModel->insert([
            'nama'  => $this->request->getPost('nama'),
            'prodi' => $this->request->getPost('prodi'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }

    public function hapus($id)
    {
        $this->mahasiswaModel->delete($id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function getById($id)
    {
        $data = $this->mahasiswaModel->find($id);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function update($id)
    {
        $this->mahasiswaModel->update($id, [
            'nama'  => $this->request->getPost('nama'),
            'prodi' => $this->request->getPost('prodi'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil diupdate'
        ]);
    }
}
