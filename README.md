# Dingxin Symotech – Distribution & Daily Cash Settlement Management System

> Aplikasi Web Manajemen Distribusi Barang, Faktur Kredit, Konfirmasi Transfer, dan Rekonsiliasi Setoran Harian Armada Sopir PT Symo Tech.  
> Target Domain: **`https://dingxin.symotech.web.id`**

---

## 🚀 Ringkasan & Arsitektur Sistem

Aplikasi ini dibangun menggunakan arsitektur modern:
- **Backend Core**: Laravel 11.x (PHP 8.2+)
- **Admin, Kasir, & GM Portal**: Filament v3 (Panel berbasis Livewire & Tailwind CSS)
- **Driver Mobile Portal**: Mobile-First Blade Views + Tailwind CSS responsif ramah layar smartphone lapangan
- **Spreadsheet Engine**: `phpoffice/phpspreadsheet` untuk import Excel barang bawaan harian
- **Database Engine**: Multi-driver (SQLite untuk lokal & testing instan, MySQL/MariaDB untuk server produksi/cPanel)

---

## 🔑 Kredensial Akun Default (Seeder)

Seluruh akun di bawah ini telah di-seed ke sistem dengan kata sandi default: **`password`**

### 1. Tim Manajemen & Operasional Kantor (Login via `/admin/login`)
| Role | Username | Nama Akun | Akses Menu |
| :--- | :--- | :--- | :--- |
| **GM (Administrator)** | `gm` | General Manager | Seluruh menu sistem, audit transaksi, & manajemen pengguna |
| **Kasir Utama** | `kasir` | Kasir Utama | Konfirmasi transfer toko, input setoran tunai kasir, & rekapitulasi setoran all sopir |
| **Admin Penjualan 1** | `admin1` | Admin Penjualan 1 | Master Sopir, input bawaan (manual & upload Excel), & input barang kembali |
| **Admin Penjualan 2** | `admin2` | Admin Penjualan 2 | Operasional penjualan armada |
| **Admin Penjualan 3** | `admin3` | Admin Penjualan 3 | Operasional penjualan armada |

### 2. Armada Sopir Lapangan (Login via `/login` atau `/driver/login`)
Sopir login menggunakan **Kode Sopir** masing-masing:

- **Wilayah Tegal (TGL - 14 Kode / 7 Armada Pasangan)**:
  `TGL1.2`, `TGL3.4`, `TGL5.6`, `TGL7.8`, `TGL9.10`, `TGL11.12`, `TGL13.14`
- **Wilayah Brebes (BRS - 14 Kode / 7 Armada Pasangan)**:
  `BRS1.2`, `BRS3.4`, `BRS5.6`, `BRS7.8`, `BRS9.10`, `BRS11.12`, `BRS13.14`

> **Catatan Isolasi Data**: Sopir yang login hanya dapat melihat mutasi, ringkasan wajib setor, dan riwayat transaksi miliknya sendiri.

---

## 🧮 Formula Bisnis & Rekonsiliasi Keuangan

$$\text{Bawaan Bersih} = \text{Barang Dibawa} - \text{Barang Kembali (Retur)}$$
$$\text{Wajib Setor Kasir} = \text{Bawaan Bersih} - \text{Transfer Dikonfirmasi} - \text{Faktur Kredit}$$
$$\text{Selisih Setor} = \text{Wajib Setor} - \text{Sudah Setor (Uang Tunai Diterima Kasir)}$$

### Indikator Status:
- $\text{Selisih} = 0$: **LUNAS / PAS** (Badge Hijau)
- $\text{Selisih} > 0$: **KURANG SETOR / MINUS** (Badge Merah/Amber)
- $\text{Selisih} < 0$: **LEBIH SETOR / SURPLUS** (Badge Biru)

---

## 📋 Fitur Utama Per Menu

### A. Menu Admin Penjualan (`/admin`)
1. **Input Bawaan Sopir (Rupiah)**:
   - Input manual per sopir per tanggal.
   - **Upload File Excel**: Menggunakan `phpoffice/phpspreadsheet`. Mendukung format kolom `[Tanggal, Kode Sopir, Nominal Bawaan, Keterangan/Rute]`.
   - **Download Template Excel**: Template standar berekstensi `.xlsx` siap pakai.
2. **Input Barang Kembali (Retur)**:
   - Pencatatan nominal rupiah retur/barang rusak per sopir sebagai pos pemotong beban bawaan.
3. **Master Sopir**:
   - Pengelolaan kode sopir, wilayah (TGL/BRS), nama, nomor HP WhatsApp, nomor plat armada, dan status aktif.

### B. Menu Kasir (`/admin`)
1. **Konfirmasi Transfer Toko**:
   - Menampilkan klaim transfer dari sopir beserta bukti foto transfer.
   - Aksi **Approve**: Konfirmasi mutasi dengan opsi penyesuaian nominal riil bank.
   - Aksi **Reject**: Penolakan klaim dengan catatan alasan yang langsung terbaca di HP sopir.
2. **Menu Setoran All Sopir (`/admin/daily-settlement-summary`)**:
   - Filter tanggal operasional.
   - Tabel realtime seluruh sopir: Bawaan, Retur, Transfer Approved, Kredit, Wajib Setor, Sudah Setor, dan Selisih.
   - Tombol **`+ Input Setor`**: Modal kasir untuk mencatat penerimaan uang tunai (mendukung multi-tahap setoran bertahap).
   - **BARIS ALL SETOR (Footer Table)**: Akumulasi total seluruh kolom untuk seluruh sopir pada tanggal tersebut.

### C. Menu Sopir (`/driver`)
1. **Dashboard Statistik Finansial**:
   - Kartu Utama: Wajib Setor Kasir vs Sudah Disetor vs Status Selisih.
   - 4 Kartu Breakdown: Bawaan, Retur, Transfer (disetujui & pending), Kredit Toko.
2. **Input Pembayaran Transfer**:
   - Nama toko (bebas ketik, wajib), nominal (wajib), upload foto bukti transfer (opsional).
3. **Input Pengiriman Kredit Toko**:
   - Nama toko (wajib), nominal (wajib), upload foto fisik faktur bertanda tangan (WAJIB).
4. **Riwayat Mutasi Transaksi**:
   - Daftar riwayat transaksi hari ini beserta badge status verifikasi kasir.

---

## 🛠️ Instalasi & Menjalankan di Server Lokal / Produksi

```bash
# 1. Masuk ke direktori
cd dingxin_symotech

# 2. Salin environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Buat symlink public storage untuk upload gambar
php artisan storage:link

# 5. Jalankan migrasi dan seeder
php artisan migrate --seed

# 6. Jalankan server lokal
php artisan serve
```

### Konfigurasi MySQL Server / cPanel:
Ubah bagian database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_mysql
DB_USERNAME=user_mysql
DB_PASSWORD=password_mysql
```
Lalu jalankan `php artisan migrate:fresh --seed`.

---

## 🧪 Pengujian Otomatis (Automated Testing)

Aplikasi dilengkapi dengan rangkaian automated test menyeluruh:
```bash
php artisan test
```
- ✅ **DingxinBusinessLogicTest**: Verifikasi seeder, formula matematika rekonsiliasi, verifikasi transfer kasir, multi-setoran kasir, dan import Excel.
- ✅ **DriverPortalTest**: Verifikasi otentikasi kode sopir, session guard isolasi data, upload bukti transfer, validasi foto faktur wajib, dan pergerakan mutasi harian.
