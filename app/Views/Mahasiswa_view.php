<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa | PWL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 1px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }
        .table thead th {
            background-color: #1565C0;
            color: white;
            border: none;
            font-weight: 500;
        }
        .table tbody tr:hover {
            background-color: #e3f0ff;
        }
        .btn-action {
            padding: 4px 10px;
            font-size: 13px;
        }
        .badge-prodi {
            background-color: #e3f0ff;
            color: #1565C0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .modal-header {
            background-color: #1565C0;
            color: white;
        }
        .modal-header .btn-close {
            filter: invert(1);
        }
        #loadingSpinner {
            display: none;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark" style="background-color: #0D3B7A;">
    <div class="container">
        <span class="navbar-brand">
            <i class="bi bi-mortarboard-fill me-2"></i>Sistem Data Mahasiswa
        </span>
        <span class="text-white-50 small">Pemrograman Web Lanjut</span>
    </div>
</nav>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Data Mahasiswa</h5>
            <small class="text-muted">Kelola data mahasiswa dengan mudah</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalForm" onclick="resetForm()">
            <i class="bi bi-plus-lg me-1"></i>Tambah Mahasiswa
        </button>
    </div>

    <!-- Alert Box -->
    <div id="alertBox" class="alert d-none" role="alert"></div>

    <!-- Tabel -->
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <span id="loadingSpinner" class="spinner-border spinner-border-sm text-primary me-2"></span>
                Total: <strong id="totalData">0</strong> mahasiswa
            </span>
            <button class="btn btn-outline-secondary btn-sm" onclick="loadData()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;" class="ps-4">No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Program Studi</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="bi bi-hourglass-split me-2"></i>Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Form Tambah / Edit -->
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah Mahasiswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Mahasiswa</label>
                    <input type="text" class="form-control" id="nama" placeholder="Masukkan nama lengkap">
                    <div class="invalid-feedback">Nama tidak boleh kosong.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Program Studi</label>
                    <select class="form-select" id="prodi">
                        <option value="">-- Pilih Prodi --</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Sipil">Teknik Sipil</option>
                    </select>
                    <div class="invalid-feedback">Prodi harus dipilih.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpan">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Hapus Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-1">Yakin ingin menghapus data</p>
                <strong id="namaHapus"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnHapusKonfirm">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    // Base URL — sesuaikan jika perlu
    const BASE_URL = window.location.origin + '/<?= config('App')->indexPage ? config('App')->indexPage . '/' : '' ?>mahasiswa';

    // ─── Load Data ────────────────────────────────────────────────────────────
    function loadData() {
        $('#loadingSpinner').show();
        $('#tbody').html('<tr><td colspan="4" class="text-center py-3 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...</td></tr>');

        $.ajax({
            url: BASE_URL + '/getData',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.status === 'success') {
                    let html = '';
                    if (response.data.length === 0) {
                        html = '<tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>Belum ada data mahasiswa.</td></tr>';
                    } else {
                        response.data.forEach(function(row, index) {
                            html += `
                                <tr>
                                    <td class="ps-4 text-muted">${index + 1}</td>
                                    <td><i class="bi bi-person me-2 text-primary"></i>${row.nama}</td>
                                    <td><span class="badge-prodi">${row.prodi}</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-action me-1" onclick="editData(${row.id})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-danger btn-action" onclick="konfirmasiHapus(${row.id}, '${row.nama}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    }
                    $('#tbody').html(html);
                    $('#totalData').text(response.data.length);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                showAlert('Gagal memuat data. Pastikan server berjalan.', 'danger');
                $('#tbody').html('<tr><td colspan="4" class="text-center py-3 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat data.</td></tr>');
                console.error('Error:', xhr.responseText);
            }
        });
    }

    // ─── Reset Form ───────────────────────────────────────────────────────────
    function resetForm() {
        $('#editId').val('');
        $('#nama').val('');
        $('#prodi').val('');
        $('#modalTitle').html('<i class="bi bi-person-plus-fill me-2"></i>Tambah Mahasiswa');
        $('#nama').removeClass('is-invalid');
        $('#prodi').removeClass('is-invalid');
    }

    // ─── Simpan (Tambah) ──────────────────────────────────────────────────────
    $('#btnSimpan').click(function() {
        const nama  = $('#nama').val().trim();
        const prodi = $('#prodi').val();
        const id    = $('#editId').val();

        // Validasi
        let valid = true;
        if (!nama) { $('#nama').addClass('is-invalid'); valid = false; } else { $('#nama').removeClass('is-invalid'); }
        if (!prodi) { $('#prodi').addClass('is-invalid'); valid = false; } else { $('#prodi').removeClass('is-invalid'); }
        if (!valid) return;

        const url    = id ? BASE_URL + '/update/' + id : BASE_URL + '/simpan';
        const method = 'POST';

        $.ajax({
            url: url,
            method: method,
            data: { nama: nama, prodi: prodi },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('modalForm')).hide();
                    showAlert(res.message, 'success');
                    loadData();  // ✅ Dipanggil dengan () yang benar
                }
            },
            error: function(xhr) {
                showAlert('Terjadi kesalahan saat menyimpan.', 'danger');
                console.error('Error:', xhr.responseText);
            }
        });
    });

    // ─── Edit Data ────────────────────────────────────────────────────────────
    function editData(id) {
        $.ajax({
            url: BASE_URL + '/getById/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#editId').val(res.data.id);
                    $('#nama').val(res.data.nama);
                    $('#prodi').val(res.data.prodi);
                    $('#modalTitle').html('<i class="bi bi-pencil-square me-2"></i>Edit Mahasiswa');
                    new bootstrap.Modal(document.getElementById('modalForm')).show();
                }
            }
        });
    }

    // ─── Hapus ────────────────────────────────────────────────────────────────
    let hapusId = null;

    function konfirmasiHapus(id, nama) {
        hapusId = id;
        $('#namaHapus').text(nama);
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    }

    $('#btnHapusKonfirm').click(function() {
        $.ajax({
            url: BASE_URL + '/hapus/' + hapusId,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('modalHapus')).hide();
                    showAlert(res.message, 'success');
                    loadData();
                }
            },
            error: function(xhr) {
                showAlert('Gagal menghapus data.', 'danger');
            }
        });
    });

    // ─── Alert Helper ─────────────────────────────────────────────────────────
    function showAlert(msg, type) {
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        $('#alertBox')
            .removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type)
            .html(`<i class="bi ${icon} me-2"></i>${msg}`);
        setTimeout(() => $('#alertBox').addClass('d-none'), 3000);
    }

    // ─── Init ─────────────────────────────────────────────────────────────────
    $(document).ready(function() {
        loadData();
    });
</script>

</body>
</html>
