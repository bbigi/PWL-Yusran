# SOAL 3 — Analisis & Perbaikan Kode CRUD AJAX

> **Mata Kuliah:** Pemrograman Web Lanjut | **Nama:** Yusran  
> **Universitas Sarjanawiyata Tamansiswa — Informatika, Fakultas Teknik**

---

## Daftar Kesalahan yang Ditemukan

Berdasarkan analisis kode (`View/Mahasiswa_view.php`, `Controller/mahasiswa.php`, `Model/MahasiswaModel`), ditemukan **7 kesalahan kritis**.

---

## ❌ Kesalahan 1 — JavaScript: `loadData` dipanggil tanpa tanda kurung

**Lokasi:** View — Baris 38

**Kode Bermasalah:**
```javascript
success: function(res) {
    alert('Data berhasil disimpan');
    loadData;   // ← SALAH! Ini referensi fungsi, bukan pemanggilan
}
```

**Penyebab:**  
`loadData` tanpa `()` hanya mereferensikan fungsi sebagai nilai, tidak memanggilnya. Ini kesalahan logika JavaScript yang sangat umum.

**Dampak:**  
Setelah data berhasil disimpan, tabel tidak akan diperbarui. User harus refresh halaman manual untuk melihat data baru.

---

## ❌ Kesalahan 2 — JavaScript: Selector `#tbody` tidak ada di HTML

**Lokasi:** View — Baris 20

**Kode Bermasalah:**
```javascript
$("#tbody").html(html);   // ← Menggunakan ID selector #tbody
```

**Penyebab:**  
Elemen `<tbody>` di HTML jarang diberi atribut `id='tbody'`. Jika HTML menggunakan `<tbody>` tanpa id, jQuery tidak akan menemukan elemen tersebut.

**Dampak:**  
Data yang diterima dari server tidak akan pernah ditampilkan di tabel. Tabel akan selalu kosong meskipun data ada di database.

---

## ❌ Kesalahan 3 — Controller: Menggunakan `return json_encode()`

**Lokasi:** Controller — Baris 7

**Kode Bermasalah:**
```php
public function getData() {
    $model = new MahasiswaModel();
    $data = $model->findAll();
    return json_encode($data);  // ← SALAH untuk CodeIgniter 4!
}
```

**Penyebab:**  
Di CodeIgniter 4, Controller harus mengembalikan objek Response, bukan string biasa. Menggunakan `return` dengan string menyebabkan response tidak dikirim dengan header `Content-Type` yang benar.

**Dampak:**  
Browser tidak mengenali response sebagai JSON, sehingga jQuery AJAX gagal mem-parse response dan `success` callback tidak terpanggil dengan benar.

---

## ❌ Kesalahan 4 — Controller: Instansiasi Model Manual di Setiap Method

**Lokasi:** Controller — Baris 3 dan 12

**Kode Bermasalah:**
```php
$model = new MahasiswaModel();   // ← Diulang di setiap method
```

**Penyebab:**  
Membuat `new MahasiswaModel()` manual di setiap method melanggar prinsip **DRY (Don't Repeat Yourself)** dan tidak memanfaatkan arsitektur CI4 dengan benar.

**Dampak:**  
Kode lebih verbose dan tidak efisien. Jika nama Model berubah, harus diganti di setiap method satu per satu.

---

## ❌ Kesalahan 5 — Controller: `echo 'success'` bukan JSON Response

**Lokasi:** Controller — Baris 19

**Kode Bermasalah:**
```php
public function simpan() {
    // ...
    echo 'success';   // ← Response tidak konsisten
}
```

**Penyebab:**  
`getData()` mengembalikan JSON, tapi `simpan()` mengembalikan plain string `'success'`. Tidak konsisten dan tidak bisa dikembangkan.

**Dampak:**  
Jika client menggunakan `dataType: 'json'`, string `'success'` akan menyebabkan JSON parse error di console.

---

## ❌ Kesalahan 6 — Model: Tidak Ada `$allowedFields`

**Lokasi:** Model/MahasiswaModel — Baris 7

**Kode Bermasalah:**
```php
class MahasiswaModel extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'id';
    // tidak ada allowedFields   // ← SANGAT BERBAHAYA!
}
```

**Penyebab:**  
Di CodeIgniter 4, `$allowedFields` adalah whitelist kolom yang boleh diisi melalui `insert()` atau `save()`. Tanpa ini, CI4 akan menolak operasi insert/update.

**Dampak:**  
Operasi `$model->insert()` akan gagal atau melempar exception. Data tidak bisa tersimpan ke database.

---

## ❌ Kesalahan 7 — Model: Tidak Ada Namespace

**Lokasi:** Model/MahasiswaModel — Baris 1-2

**Kode Bermasalah:**
```php
<?php
class MahasiswaModel extends Model   // ← Tidak ada namespace App\Models;
```

**Penyebab:**  
Di CodeIgniter 4, setiap Model harus memiliki `namespace App\Models` dan meng-extend `CodeIgniter\Model`. Tanpa namespace, autoloader CI4 tidak bisa menemukan class ini.

**Dampak:**  
`Fatal Error: Class 'MahasiswaModel' not found` — seluruh fitur CRUD lumpuh.

---

## ✅ Solusi & Kode yang Diperbaiki

### Perbaikan View (`mahasiswa_view.php`)
*Mengatasi Kesalahan 1 dan 2*

```javascript
<script>
$(document).ready(function() {
    loadData();

    function loadData() {
        $.ajax({
            url: 'http://localhost/ci4/public/mahasiswa/getData',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                let html = '';
                response.data.forEach(function(row) {
                    html += '<tr>';
                    html += '<td>' + row.id + '</td>';
                    html += '<td>' + row.nama + '</td>';
                    html += '<td>' + row.prodi + '</td>';
                    html += '</tr>';
                });
                $('#tbody').html(html);   // ✅ Pastikan <tbody id='tbody'> di HTML
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    }

    $('#btnSimpan').click(function() {
        let nama  = $('#nama').val();
        let prodi = $('#prodi').val();

        $.ajax({
            url: 'http://localhost/ci4/public/mahasiswa/simpan',
            method: 'POST',
            data: { nama: nama, prodi: prodi },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    alert('Data berhasil disimpan');
                    loadData();   // ✅ DIPERBAIKI: loadData() dengan tanda kurung
                }
            },
            error: function(xhr) {
                console.error('Gagal simpan:', xhr.responseText);
            }
        });
    });
});
</script>
```

**Alasan perbaikan:**
- `loadData()` dengan `()` → memanggil fungsi, bukan hanya mereferensikannya
- Tambahan `error` handler → memudahkan debugging jika AJAX gagal
- `dataType: 'json'` konsisten → response dari server harus berformat JSON

---

### Perbaikan Controller (`Mahasiswa.php`)
*Mengatasi Kesalahan 3, 4, dan 5*

```php
<?php
namespace App\Controllers;   // ✅ DITAMBAHKAN: Namespace wajib di CI4

use App\Models\MahasiswaModel;

class Mahasiswa extends BaseController
{
    protected $mahasiswaModel;

    public function __construct()
    {
        // ✅ DIPERBAIKI: Instansiasi sekali di constructor
        $this->mahasiswaModel = new MahasiswaModel();
    }

    public function getData()
    {
        $data = $this->mahasiswaModel->findAll();
        // ✅ DIPERBAIKI: Gunakan $this->response dengan header JSON
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function simpan()
    {
        $this->mahasiswaModel->insert([
            'nama'  => $this->request->getPost('nama'),
            'prodi' => $this->request->getPost('prodi')
        ]);
        // ✅ DIPERBAIKI: Response JSON konsisten
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data berhasil disimpan'
        ]);
    }
}
```

**Alasan perbaikan:**
- `namespace App\Controllers` → wajib agar autoloader CI4 menemukan class
- `$this->mahasiswaModel` di constructor → DRY principle, tidak instansiasi ulang
- `$this->response->setJSON()` → mengirim header `Content-Type: application/json` secara otomatis dan benar

---

### Perbaikan Model (`MahasiswaModel.php`)
*Mengatasi Kesalahan 6 dan 7*

```php
<?php
namespace App\Models;   // ✅ DITAMBAHKAN: Namespace wajib

use CodeIgniter\Model;  // ✅ DITAMBAHKAN: Import class Model CI4

class MahasiswaModel extends Model
{
    protected $table      = 'mahasiswa';
    protected $primaryKey = 'id';

    // ✅ DITAMBAHKAN: allowedFields WAJIB untuk operasi insert/update
    protected $allowedFields = ['nama', 'prodi'];

    // Opsional tapi sangat direkomendasikan:
    protected $useTimestamps = true;    // Auto-isi created_at & updated_at
    protected $returnType    = 'array'; // Return data sebagai array
}
```

**Alasan perbaikan:**
- `namespace App\Models` → wajib agar autoloader CI4 menemukan class Model
- `use CodeIgniter\Model` → import class yang benar dari framework
- `$allowedFields = ['nama', 'prodi']` → wajib di CI4 untuk mencegah Mass Assignment Attack dan memungkinkan operasi insert/update berhasil
- `$useTimestamps` → best practice untuk audit trail data

---

## Ringkasan

| No | Kesalahan | Solusi |
|----|-----------|--------|
| 1 | `loadData;` tanpa `()` di JS | Ubah menjadi `loadData();` |
| 2 | Selector `#tbody` tidak sesuai HTML | Pastikan elemen HTML memiliki `id='tbody'` |
| 3 | `return json_encode()` di Controller | Gunakan `return $this->response->setJSON()` |
| 4 | Instansiasi Model manual tiap method | Pindahkan ke `__construct()` dengan `$this->mahasiswaModel` |
| 5 | `echo 'success'` tidak konsisten | Gunakan `return $this->response->setJSON(['status'=>'success'])` |
| 6 | Tidak ada `$allowedFields` di Model | Tambahkan `protected $allowedFields = ['nama', 'prodi']` |
| 7 | Tidak ada namespace di Model/Controller | Tambahkan `namespace App\Models;` dan `namespace App\Controllers;` |


##Hasil
<img width="923" height="473" alt="image" src="https://github.com/user-attachments/assets/21fcda7e-8d3d-495f-aa13-04f748e226e3" />
<img width="903" height="466" alt="image" src="https://github.com/user-attachments/assets/ed54a4f9-5c13-41b8-907a-af0f67849fb7" />
<img width="918" height="472" alt="image" src="https://github.com/user-attachments/assets/86f2671d-dcb2-4d4a-a1d9-c2d50a65dcd4" />
<img width="911" height="467" alt="image" src="https://github.com/user-attachments/assets/6ca5877a-c37e-4f05-8bce-9cc9ed7941f3" />
<img width="912" height="470" alt="image" src="https://github.com/user-attachments/assets/a00047f3-3423-455c-9e53-f62ceb17ebd5" />
