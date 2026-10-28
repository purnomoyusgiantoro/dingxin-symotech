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
| **Kasir** | `kasir` | `/admin` (Filament Panel) | • Verifikasi & konfirmasi pembayaran transfer (Approve dengan penyesuaian mutasi / Reject dengan alasan)<br>• Menu Setoran All Sopir + input setoran tunai kasir (multi-tahap)<br>• Modal inspeksi rincian setoran per sopir<br>• Rekapitulasi dengan **Baris All Setor**<br>• Export Rekapitulasi Setoran ke format Excel (.xlsx) |
| **GM (Administrator)**| `gm` | `/admin` (Filament Panel) | • Akses menyeluruh (*Full Access*) ke seluruh resource dan rekapitulasi<br>• **Executive Dashboard Widget**: Ringkasan armada aktif, total muatan, uang masuk, dan selisih harian<br>• **Wewenang Eksklusif Koreksi Setoran**: Satu-satunya role yang berhak membatalkan/menghapus setoran kasir<br>• Export Rekapitulasi Setoran ke Excel (.xlsx) |

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

## 🔐 Arsitektur Autentikasi Tunggal Terpadu (Unified Clean White Login)
1. **Satu Pintu Masuk Resmi Putih Polos**:
   - Akses `/login` maupun `/admin/login` menyajikan antarmuka **putih polos minimalis** yang seragam, bersih, dan profesional tanpa teks contekan atau tombol demo yang mengganggu.
2. **Auto-Redirection Cerdas**:
   - Sopir login $\rightarrow$ otomatis diarahkan ke portal mobile `/driver`.
   - Kasir / Admin / GM login $\rightarrow$ otomatis diarahkan ke panel manajemen `/admin`.
3. **Pencarian Akun Fleksibel**:
   - Mendukung username huruf besar/kecil (`tgl1.2` atau `TGL1.2`).
   - Mendukung login menggunakan email atau kode armada.
   - Shortcut alias: mengetik `sopir` otomatis mengarahkan ke akun armada `TGL1.2`.
4. **Sesi Cookie Persistent (Remember Me)**:
   - Checkbox *"Ingat saya di perangkat ini"* tersedia di login desktop Filament (`App\Filament\Pages\Auth\Login`) dan portal driver (`resources/views/driver/login.blade.php`), aktif secara default (`default(true)`).
   - Menerbitkan cookie terenkripsi `remember_web_*` berdurasi jangka panjang dengan flag `HttpOnly = true` dan `SameSite = Lax` untuk mitigasi serangan XSS dan CSRF.
   - **Bypass Redirection Guard**: Pada `Login::mount()`, user dengan role driver yang memiliki cookie remember langsung diarahkan ke `route('driver.dashboard')` tanpa tersangkut error `403 Forbidden` di panel admin Filament.
   - **Invalidasi Token Saat Logout**: Method `DriverAuthController::logout()` secara proaktif merotasi `remember_token` di database (`Str::random(60)`) untuk mencegah serangan token replay.
   - **Perlindungan Session Fixation & Akun Nonaktif**: Session ID di-regenerate saat login, dan akun berstatus `is_active = false` langsung ditolak dan dipaksa logout meskipun membawa cookie remember valid.
   - Seluruh audit keamanan diuji secara otomatis pada test suite `Tests\Feature\AuthSecurityAndRememberMeTest` (8/8 skenario lolos).

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

## 📱 Antarmuka Portal Sopir Bersih & Ramah Lansia (Clean & Senior-Friendly)
Layanan tampilan sopir (`resources/views/driver/*`):
- **Prinsip Desain**: Bersih tanpa informasi tambahan berlebih (*zero clutter*), kontras tinggi, proporsional, ramah pengguna berusia lanjut (*elderly-friendly UX*), dan mudah dioperasikan dengan jempol tangan di smartphone.
- **Standar Proporsi & Tipografi**:
  - Ukuran font proporsional standar (15-16px untuk label form & teks baca, 20-28px monospace untuk angka nominal uang).
  - Tombol aksi utama dengan tinggi standar ergonomis (48-52px), tidak terlalu raksasa agar tetap rapi, modern, dan bagus dipandang.
  - Hapus seluruh elemen dekoratif yang mengganggu (animasi pulsing dot, teks jargon berbelit, sub-label ganda).
- **Palet Warna**:
  1. **Putih Bersih (`#FFFFFF`) / Slate Lembut (`#F8FAFC`)**: Latar belakang aplikasi, kartu informasi, formulir input.
  2. **Dark Slate (`#0F172A`)**: Header minimalis, tombol aksi utama, badge status tegas, dan angka penting.
- **Pemisahan Tegas UI vs. Logika**:
  - Seluruh `id` & `name` input (`claimed_amount_display`, `claimed_amount`, `amount_display`, `amount`, `proof_image`, `invoice_photo`, `notes`, `store_name`) dan JavaScript sinkronisasi Rupiah dipertahankan 100%.
  - Seluruh suite pengujian otomatis PHPUnit (25/25) dan Playwright E2E browser (20/20) terverifikasi lulus 100%.

---

## 🎨 Sistem Aset Ikon Vektor Lokal (Self-Hosted SVG Icon System)
- **Struktur Direktori**: Tersimpan langsung di direktori publik `public/icons/` terbagi rapi berdasarkan kategori penggunaan:
  - `public/icons/nav/`: `home.svg`, `transfer.svg`, `credit.svg`, `history.svg`, `logout.svg`
  - `public/icons/actions/`: `plus-circle.svg`, `file-plus.svg`, `camera.svg`, `send.svg`, `check-square.svg`, `arrow-left.svg`, `eye.svg`, `search.svg`
  - `public/icons/status/`: `success.svg`, `warning.svg`, `danger.svg`, `info.svg`
  - `public/icons/types/`: `truck.svg`, `return.svg`, `bank.svg`, `receipt.svg`, `cash.svg`, `calendar.svg`, `inbox.svg`
- **Blade Component Khusus**: [`resources/views/components/app-icon.blade.php`](file:///D:/Documents/symotech_projek/dingxin_symotech/resources/views/components/app-icon.blade.php)
  - Menggunakan tag `<x-app-icon name="category.name" class="..." />` (penamaan `app-icon` dipilih secara spesifik untuk menghindari tabrakan nama dengan `blade-ui-kit/blade-icons` pada Filament).
  - Merender SVG inline secara otomatis sehingga mendukung styling warna Tailwind (`text-slate-700`, `text-emerald-600`, dll) dan ukuran responsif (`w-4 h-4`, `w-5 h-5`).
  - **Zero External CDN Dependency**: Bebas dari ketergantungan script CDN eksternal (`unpkg.com/lucide`), instan di-render, tajam di semua densitas layar HP sopir, dan 100% aman saat koneksi internet sopir sedang lambat di lapangan.

---

## 🖥️ Panel Kasir & Rekapitulasi Setoran Harian All Sopir
Halaman: [`resources/views/filament/pages/daily-settlement-summary.blade.php`](file:///D:/Documents/symotech_projek/dingxin_symotech/resources/views/filament/pages/daily-settlement-summary.blade.php)
- **3 Kartu Ringkasan Eksekutif (Stats Overview)**:
  - Disusun menggunakan grid 3 kolom (`.settlement-overview-grid`) dengan struktur *Header (Judul + Ikon)* di baris atas dan *Angka Nilai Monospace* di baris bawah.
  - Tipografi seimbang dengan `white-space: nowrap`, menjamin angka (`Rp 19.000.000` / `[KURANG] Rp 3.000.000`) tidak pernah patah ke baris kedua pada resolusi laptop (1366x768 & 1280x800).
- **Tabel Rekapitulasi Sopir & Standar Tinggi Baris Seragam (Uniform 52px Row Height)**:
  - Diberikan jarak pemisah yang nyaman (`margin-top: 1.5rem`) dari kartu ringkasan.
  - **Tinggi Baris Presisi & Seragam**:
    - Header `thead tr / th`: tinggi pasti `48px`.
    - Isi `tbody tr / td`: tinggi pasti **`52px`** seragam untuk seluruh baris, dengan konten rata tengah vertikal (`vertical-align: middle !important`). Tidak ada baris yang menciut atau menggelembung.
    - Footer `tfoot tr / td`: tinggi pasti `54px` (Baris All Setor).
    - Seluruh tabel bawaan Filament (`.fi-ta-table`) diinjeksi rule tinggi seragam serupa melalui `AdminPanelProvider` renderHook.
  - Setiap sel tabel (`th` dan `td`) memiliki pembatas baris horizontal (`border-bottom`) dan **garis pemisah vertikal antar kolom (`border-right: 1px solid #e2e8f0` / dark: `border-slate-700`)** sehingga batas kolom angka sangat tegas, rapi, dan mudah dibaca tanpa saling tumpang tindih.
  - Dilengkapi scrollbar horizontal elegan dan badge petunjuk visual interaktif: `↔ Geser tabel untuk kolom Status & Aksi`.
  - Kolom *Transfer Conf.* & *Sudah Setor* menampilkan badge pill rapi dalam satu baris sejajar flex (`+ Pnd: Rp ...` / `2x`), mempertahankan tinggi baris tetap 52px.
  - Kolom *Aksi Kasir* dilengkapi tombol input setor biru tebal (tinggi pas 32px) dengan modal Livewire multi-setor yang aman dan teruji.

---

## 🛠️ Perintah Operasional & Pengujian

```bash
# Menjalankan server lokal
php artisan serve --port=8000

# Menjalankan seluruh automated test suite (25 tests passing, 150 assertions)
php artisan test

# Menjalankan database migration & sample seed
php artisan migrate:fresh --seed

# Memastikan symlink storage gambar aktif
php artisan storage:link

# Menjalankan automated Playwright browser E2E test suite lintas seluruh akun (20 skenario 100% pass)
uv run --with playwright python -u tests/E2E/test_master_web_e2e.py
```

---

## 🚀 Prosedur Deployment Produksi cPanel (`dingxin.symotech.web.id`)

### 1. Berkas Paket Deployment Siap Pakai
- **ZIP Aplikasi Mandiri**: [`dingxin.symotech.web.id.zip`](file:///D:/Documents/symotech_projek/dingxin_symotech/dingxin.symotech.web.id.zip) (20.17 MB)
  - Berisi seluruh source code, vendor paket produksi, asset SVG, dan [.htaccess](file:///D:/Documents/symotech_projek/dingxin_symotech/.htaccess) root cPanel.
  - Siap di-upload ke cPanel File Manager pada direktori root domain `dingxin.symotech.web.id/` dan di-extract langsung.
- **Ekspor Basis Data MySQL**: [`database/dingxin_symotech_production.sql`](file:///D:/Documents/symotech_projek/dingxin_symotech/database/dingxin_symotech_production.sql) (54.6 KB)
  - 16 tabel lengkap dengan relasi Foreign Key, index unik, dan 20 akun pengguna awal (GM, Kasir, Admin, 14 Sopir Armada TGL & BRS).
  - Siap diimpor langsung via phpMyAdmin ke database MySQL cPanel (contoh: `symotec3_dingxin`).

### 2. Konfigurasi Lingkungan (`.env`) di cPanel
Salin [`.env.production.example`](file:///D:/Documents/symotech_projek/dingxin_symotech/.env.production.example) menjadi `.env` pada server cPanel:
```dotenv
APP_NAME="Dingxin Symotech"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://dingxin.symotech.web.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=symotec3_dingxin
DB_USERNAME=symotec3_dingxinuser
DB_PASSWORD=••••••••••••
```

### 3. Otomasi CI/CD GitHub Actions
- [`.github/workflows/deploy.yml`](file:///D:/Documents/symotech_projek/dingxin_symotech/.github/workflows/deploy.yml)
- [`scripts/cpanel_deploy.py`](file:///D:/Documents/symotech_projek/dingxin_symotech/scripts/cpanel_deploy.py)
- Secret terdaftar di GitHub: `CPANEL_FTP_SERVER`, `CPANEL_FTP_USERNAME`, `CPANEL_FTP_PASSWORD`.

---

## 🎨 Standardisasi Identitas Visual & Clean Fintech Redesign Portal Sopir

Berdasarkan optimasi UX mobile-first dan brand identity resmi PT Dingxin Boga Indonesia (Distributor Resmi Aice):
1. **Identitas Logo & Header Kompak**:
   - Berkas: [`public/images/dingxin_logo.png`](file:///D:/Documents/symotech_projek/dingxin_symotech/public/images/dingxin_logo.png) (Aice - Have An Aice Day).
   - **Kiri**: Brand Group memuat logo Aice Dingxin dan badge pill `Driver Ops` berbingkai biru halus.
   - **Kanan**: Driver Pill kompak (`BS • Budi S.` / `TGL1.2`) yang langsung memperlihatkan identitas sopir tanpa memakan ruang konten, membuka menu profil/drawer lengkap (plat armada, area, dan tombol keluar akun) saat diklik.
2. **Navigasi Bawah Terpadu 3 Menu (Thumb-Friendly Bottom Nav)**:
   - Dirampingkan dari 4 menu menjadi **3 menu terpadu**: `Beranda`, `Catat Setoran`, dan `Riwayat`.
   - Dilengkapi garis indikator aktif di bagian atas tab (`before:absolute before:top-0 before:w-8 before:h-0.5 before:bg-slate-900 before:rounded-b`).
3. **Beranda Bebas Duplikasi & Rincian Struk (Receipt List)**:
   - **Hero Settlement Card**: Gradien slate modern (`#0F172A` ke `#1E293B`) dengan aksen radial glow, status badge pill, nominal sisa wajib setor, **progress bar pelunasan visual**, dan 2 kolom metrik (Total Wajib Setor & Sudah Tercatat/Sah).
   - **Pembersihan Tombol Duplikat**: 2 tombol raksasa di tengah beranda dihapus total, menghemat >120px ruang vertikal layar HP.
   - **Rincian Operasional Bergaya Struk**: Menggantikan 4 kotak abu-abu besar dengan daftar vertikal hemat ruang bergaris putus-putus dan indikator dot warna (Barang Dibawa, Barang Retur, Transfer Sah, Nota Bon) serta pill Net Bawaan.
   - **Catatan Hari Ini & Empty State Kontekstual**: Daftar mutasi bergaya fintech dengan ikon semantik berlatar lembut dan tautan inspeksi foto, disertai empty state ramah dengan tombol aksi cepat (`+ Catat Transfer` & `+ Catat Bon`).
4. **Catat Setoran Terpadu (Segmented Toggle & Quick Chips)**:
   - Segmented control di atas formulir (`Transfer Toko` vs `Bon / Kredit`) menghubungkan formulir transfer dan bon kredit secara mulus tanpa scroll panjang.
   - **Quick Nominal Chips**: Tombol cepat nominal (`500 Rb`, `1 Jt`, `2,5 Jt`, `5 Jt`) otomatis mengisi input dan sinkron dengan format Rupiah.
   - **Integrated Upload Box**: Kotak upload modern dengan preview foto instan; untuk faktur bon, peringatan wajib nota fisik dan cap toko diintegrasikan langsung ke dalam box upload (*required-zone*).
5. **Riwayat Akordion Interaktif (Expandable Timeline)**:
   - Menggantikan 4 kartu terpisah dengan daftar akordion interaktif yang bisa dibuka-tutup di tempat.
   - Filter pill instan (`Semua`, `Kurang`, `Lunas`) untuk memindai status harian secara cepat tanpa reload halaman.
   - Kotak rincian 3 metrik (Wajib Setor, Disetor Kasir, Selisih) dan daftar mutasi langsung tampil di dalam kartu tanggal yang dibuka.
6. **Zero-Regression Guarantee & Automated Verification**:
   - Seluruh kontrak formulir (`#transferForm`, `#creditForm`, `#claimed_amount_display`, `#claimed_amount`, `#amount_display`, `#amount`, `name="proof_image"`, `name="invoice_photo"`, `name="notes"`) dipertahankan 100%.
   - **PHPUnit Feature Tests**: 25/25 PASS (150 assertions).
   - **Playwright Browser E2E Master Tests**: 20/20 PASS (100% lolos lintas Admin, Sopir TGL1.2, Kasir, GM, dan Security Guards).





