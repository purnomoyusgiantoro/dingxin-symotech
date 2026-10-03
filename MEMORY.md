# Dingxin Symotech - Panduan & Memori Proyek

Dokumen ini berfungsi sebagai **Persistent Project Memory** untuk sistem manajemen distribusi, setoran harian sopir, verifikasi kasir, dan pelaporan keuangan operasional domain `dingxin.symotech.web.id`.

---

## 📌 Ringkasan Sistem & Arsitektur
- **Aplikasi**: Sistem Distribusi & Setoran Sopir PT Dingxin Multi Distribusi
- **Domain Target**: `dingxin.symotech.web.id`
- **Repositori GitHub**: [https://github.com/purnomoyusgiantoro/dingxin-symotech.git](https://github.com/purnomoyusgiantoro/dingxin-symotech.git) (`main`)
- **Direktori Lokal**: `D:\Documents\symotech_projek\dingxin_symotech`
- **Tech Stack**:
  - **Framework Backend**: Laravel 11.x + PHP 8.2+ (PHP Herd-Lite 8.5)
  - **Panel Manajemen**: Filament v3 (`/admin`) untuk Kasir, Admin Penjualan, dan GM
  - **Portal Sopir Mobile**: Blade + Tailwind CSS CDN (`/driver`) ramah layar smartphone / mobile-first
  - **Otomasi Spreadsheet**: `phpoffice/phpspreadsheet` untuk import Excel bawaan barang sopir
  - **Database**:
    - Development: SQLite (`database/database.sqlite`)
    - Testing: SQLite `:memory:` (`phpunit.xml`)
    - Production: MySQL / MariaDB (`utf8mb4_unicode_ci`)

---

## 👥 Peran Pengguna & Batasan Hak Akses (Role Matrix)

| Peran (Role) | Default User | Akses Halaman | Kewenangan Utama |
| :--- | :--- | :--- | :--- |
| **Sopir (Driver)** | `TGL1.2` s/d `TGL13.14`<br>`BRS1.2` s/d `BRS13.14`<br>*(Alias: `sopir`)* | `/driver` (Portal Mobile) | • Input transfer pembayaran toko (nama toko, nominal, bukti foto opsional)<br>• Input faktur kredit toko (nama toko, nominal, **foto faktur wajib**)<br>• Dashboard metrik keuangan harian (terisolasi 100% pada data dirinya sendiri) |
| **Admin Penjualan** | `admin`, `admin1`, `admin2`, `admin3` | `/admin` (Filament Panel) | • Input bawaan harian sopir dalam Rupiah (manual / upload file Excel)<br>• Input barang kembali (retur) per sopir<br>• Master data armada sopir |
| **Kasir** | `kasir` | `/admin` (Filament Panel) | • Verifikasi & konfirmasi pembayaran transfer (Approve dengan penyesuaian mutasi / Reject dengan alasan)<br>• Menu Setoran All Sopir + input setoran tunai kasir (multi-tahap)<br>• Rekapitulasi dengan **Baris All Setor** |
| **GM (Administrator)**| `gm` | `/admin` (Filament Panel) | • Akses menyeluruh (*Full Access*) ke seluruh resource, log, dan rekapitulasi |

> **Password Default Seluruh Akun**: `password`

---

## 🚚 Master Data Kode Armada Sopir

Sistem menggunakan 2 wilayah operasional dengan 14 armada berpasangan:
1. **Wilayah Tegal (TGL)**:
   - `TGL1.2`, `TGL3.4`, `TGL5.6`, `TGL7.8`, `TGL9.10`, `TGL11.12`, `TGL13.14`
2. **Wilayah Brebes (BRS)**:
   - `BRS1.2`, `BRS3.4`, `BRS5.6`, `BRS7.8`, `BRS9.10`, `BRS11.12`, `BRS13.14`

---

## 📐 Rumus Baku Perhitungan Keuangan (Settlement Calculation Formula)

Seluruh logika keuangan dikelola secara terpusat oleh [`App\Services\SettlementCalculationService`](file:///D:/Documents/symotech_projek/dingxin_symotech/app/Services/SettlementCalculationService.php):

1. **Net Nilai Bawaan**:
   $$\text{Net Bawaan} = \text{Barang Dibawa} - \text{Barang Kembali (Retur)}$$
   *(Retur barang mengurangi beban tanggung jawab sopir).*

2. **Wajib Setor Tunai**:
   $$\text{Wajib Setor} = \text{Net Bawaan} - \text{Transfer Terkonfirmasi (Status Approved)} - \text{Nominal Kredit}$$
   *(Transfer pending atau ditolak tidak mengurangi wajib setor).*

3. **Selisih Setor Kasir**:
   $$\text{Selisih Setor} = \text{Wajib Setor} - \text{Sudah Setor Tunai}$$

4. **Indikator Status**:
   - $\text{Selisih} = 0 \rightarrow$ **Lunas** (Badge Hijau)
   - $\text{Selisih} > 0 \rightarrow$ **Kurang Setor** (Badge Merah / Oranye)
   - $\text{Selisih} < 0 \rightarrow$ **Lebih Setor** (Badge Biru)

5. **Mandatory Baris ALL SETOR**:
   - Pada halaman rekapitulasi Kasir & GM (`/admin/daily-settlement-summary`), bagian footer (`<tfoot>`) **wajib menampilkan Baris All Setor** yang menjumlahkan secara otomatis: Total Bawaan, Total Retur, Total Transfer Approved, Total Kredit, Total Wajib Setor, Total Sudah Setor, dan Total Selisih untuk seluruh armada.

---

## 📊 Format File Import Excel Bawaan Barang
Layanan: [`App\Services\ExcelDeliveryImportService`](file:///D:/Documents/symotech_projek/dingxin_symotech/app/Services/ExcelDeliveryImportService.php)

File Excel/CSV bawaan barang harus memiliki susunan 4 kolom:
| Kolom A | Kolom B | Kolom C | Kolom D |
| :--- | :--- | :--- | :--- |
| **Tanggal** | **Kode Sopir** | **Total Rupiah Bawaan** | **Keterangan / Rute** |
| `2026-10-03` | `TGL1.2` | `15000000` | Rute Margadana - Kramat |

Tersedia tombol download template Excel resmi langsung pada action panel `DailyDeliveryResource`.

---

## 🔐 Arsitektur Autentikasi Multi-Portal (Universal Cross-Login)
1. **Portal Login Terpadu**:
   - `/login` (Tampilan Mobile Portal Sopir dengan Tombol Demo Quick-Fill)
   - `/admin/login` (Panel Filament Admin dengan Cheatsheet Kredensial)
2. **Auto-Redirection Cerdas**:
   - Sopir login di `/admin/login` $\rightarrow$ otomatis diarahkan ke `/driver`.
   - Kasir / Admin / GM login di `/login` $\rightarrow$ otomatis diarahkan ke `/admin`.
3. **Pencarian Akun Fleksibel**:
   - Mendukung username huruf besar/kecil (`tgl1.2` atau `TGL1.2`).
   - Mendukung login menggunakan email atau kode armada.
   - Shortcut alias: mengetik `sopir` otomatis mengarahkan ke akun armada `TGL1.2`.

---

## 🗄️ Struktur Basis Data

1. **`users`**: Akun login, `username`, `role` (`driver`, `sales_admin`, `cashier`, `gm`), `is_active`.
2. **`drivers`**: Relasi profil armada (`driver_code`, `area_code`, `phone_number`, `plate_number`, `is_active`, `cumulative_balance`).
3. **`daily_deliveries`**: Data rupiah barang bawaan harian (`driver_id`, `date`, `amount`, `route_notes`, `created_by`). Unique composite: `(driver_id, date)`.
4. **`return_items`**: Data barang kembali/retur (`driver_id`, `date`, `amount`, `notes`, `created_by`).
5. **`transfer_payments`**: Pembayaran transfer toko (`driver_id`, `date`, `store_name`, `claimed_amount`, `verified_amount`, `proof_image_path`, `status`: pending/approved/rejected, `verified_by`, `rejection_reason`).
6. **`credit_deliveries`**: Pengiriman kredit toko (`driver_id`, `date`, `store_name`, `amount`, `invoice_photo_path`, `notes`).
7. **`cash_deposits`**: Rekaman setoran uang tunai kasir (`driver_id`, `date`, `amount_received`, `deposit_phase`, `cashier_id`, `notes`).
8. **`daily_settlements`**: Snapshot kalkulasi harian untuk efisiensi kueri dan riwayat permanen.

---

## 🛠️ Perintah Operasional & Pengujian

```bash
# Menjalankan server lokal
php artisan serve --port=8000

# Menjalankan seluruh automated test suite (22 tests passing)
php artisan test

# Menjalankan database migration & sample seed
php artisan migrate:fresh --seed

# Memastikan symlink storage gambar aktif
php artisan storage:link
```
