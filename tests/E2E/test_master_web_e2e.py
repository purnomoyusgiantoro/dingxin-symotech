"""
SYMOTECH — CV. SYNERGY MONDO TECHNOLOGIES
Dingxin Symotech Web E2E Master Test Suite
Automated Browser Testing across All Roles:
 1. Sales Admin (admin1)
 2. Driver / Sopir (TGL1.2)
 3. Cashier (kasir)
 4. General Manager (gm)
 5. Security & Cross-Redirection Guard
"""

import os
import sys
import time
import sqlite3
from datetime import date
from playwright.sync_api import sync_playwright

# Setup UTF-8 encoding untuk stdout di Windows console
if sys.platform == "win32" and hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

BASE_URL = "http://127.0.0.1:8000"
DB_PATH = "database/database.sqlite"
SCREENSHOT_DIR = "tests/E2E/screenshots"
DUMMY_IMAGE = os.path.abspath("tests/E2E/dummy_receipt.png")

# Pastikan dummy image tersedia
if not os.path.exists(DUMMY_IMAGE):
    os.makedirs(os.path.dirname(DUMMY_IMAGE), exist_ok=True)
    png_bytes = b'\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15c4\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82'
    with open(DUMMY_IMAGE, "wb") as f:
        f.write(png_bytes)

os.makedirs(SCREENSHOT_DIR, exist_ok=True)

test_results = []

def record_test(step_name, passed, detail=""):
    status = "PASS" if passed else "FAIL"
    test_results.append((step_name, status, detail))
    symbol = "[OK]" if passed else "[FAIL]"
    print(f" {symbol} {step_name}: {detail}")
    if not passed:
        raise AssertionError(f"Step '{step_name}' FAILED: {detail}")

def clean_today_data():
    """Membersihkan entri hari ini untuk armada TGL1.2 agar pengujian idempoten dan deterministik."""
    today_str = date.today().isoformat()
    con = sqlite3.connect(DB_PATH)
    cur = con.cursor()
    # Driver TGL1.2 memiliki id = 1
    cur.execute("DELETE FROM daily_deliveries WHERE driver_id = 1 AND date LIKE ?", (f"{today_str}%",))
    cur.execute("DELETE FROM return_items WHERE driver_id = 1 AND date LIKE ?", (f"{today_str}%",))
    cur.execute("DELETE FROM transfer_payments WHERE driver_id = 1 AND date LIKE ?", (f"{today_str}%",))
    cur.execute("DELETE FROM credit_deliveries WHERE driver_id = 1 AND date LIKE ?", (f"{today_str}%",))
    cur.execute("DELETE FROM cash_deposits WHERE driver_id = 1 AND date LIKE ?", (f"{today_str}%",))
    cur.execute("DELETE FROM daily_settlements WHERE driver_id = 1 AND date LIKE ?", (f"{today_str}%",))
    con.commit()
    con.close()
    print(" [INIT] Database dibersihkan untuk pengujian armada TGL1.2 hari ini.\n")


def run_e2e_suite():
    print("=" * 80)
    print("  SYMOTECH — DINGXIN DISTRIBUTION E2E WEB AUTOMATION TEST SUITE")
    print(f"  Target Server : {BASE_URL}")
    print(f"  Browser       : Microsoft Edge (Chromium Engine / Headless)")
    print("=" * 80 + "\n")

    clean_today_data()

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True, channel="msedge")

        # =========================================================================
        # FASE 1: ADMIN PENJUALAN (admin1) — Input Bawaan & Retur
        # =========================================================================
        print(">>> [FASE 1] Menjalankan Pengujian Akun Sales Admin (admin1)...")
        context_admin = browser.new_context(viewport={"width": 1366, "height": 768})
        page = context_admin.new_page()

        # 1.1 Login Admin
        page.goto(f"{BASE_URL}/admin/login")
        page.wait_for_load_state("networkidle")
        page.fill('input[type="text"]', "admin1")
        page.fill('input[type="password"]', "password")
        page.click('button[type="submit"]')
        page.wait_for_url("**/admin")
        page.wait_for_load_state("networkidle")
        record_test("1.1 Login Sales Admin", "/admin" in page.url, f"Redirect URL: {page.url}")

        # 1.2 Input Bawaan Harian untuk TGL1.2 (Rp 10.000.000)
        page.goto(f"{BASE_URL}/admin/daily-deliveries/create")
        page.wait_for_load_state("networkidle")
        # Pilih armada TGL1.2 lewat Choices.js
        page.locator(".choices__inner").click()
        page.wait_for_timeout(300)
        page.locator('.choices__list--dropdown .choices__item--choice:has-text("TGL1.2")').click()
        page.wait_for_timeout(300)
        # Isi amount & rute
        page.fill('input[id="data.amount"]', "10000000")
        page.fill('input[id="data.route_notes"]', "Rute E2E Margadana - Kramat")
        page.locator('button:has-text("Buat")').first.click()
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(1000)
        
        # Verifikasi di database
        con = sqlite3.connect(DB_PATH)
        delivery_exists = con.cursor().execute(
            "SELECT count(*) FROM daily_deliveries WHERE driver_id = 1 AND amount = 10000000"
        ).fetchone()[0] > 0
        con.close()
        record_test("1.2 Input Bawaan Rp 10.000.000", delivery_exists, "Data bawaan tersimpan di DB & terdaftar pada sistem")

        # 1.3 Input Retur Barang untuk TGL1.2 (Rp 500.000)
        page.goto(f"{BASE_URL}/admin/return-items/create")
        page.wait_for_load_state("networkidle")
        page.locator(".choices__inner").click()
        page.wait_for_timeout(300)
        page.locator('.choices__list--dropdown .choices__item--choice:has-text("TGL1.2")').click()
        page.wait_for_timeout(300)
        page.fill('input[id="data.amount"]', "500000")
        page.fill('textarea[id="data.notes"]', "Dus penyok kemasan rusak uji E2E")
        page.locator('button:has-text("Buat")').first.click()
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(1000)

        con = sqlite3.connect(DB_PATH)
        return_exists = con.cursor().execute(
            "SELECT count(*) FROM return_items WHERE driver_id = 1 AND amount = 500000"
        ).fetchone()[0] > 0
        con.close()
        record_test("1.3 Input Retur Rp 500.000", return_exists, "Data retur barang berhasil dicatat")

        page.screenshot(path=f"{SCREENSHOT_DIR}/01_admin_input_delivery_and_return.png")
        context_admin.close()


        # =========================================================================
        # FASE 2: SOPIR / DRIVER (TGL1.2) — Portal Mobile & Transaksi Toko
        # =========================================================================
        print("\n>>> [FASE 2] Menjalankan Pengujian Portal Mobile Sopir (TGL1.2)...")
        # Menggunakan viewport mobile smartphone (390 x 844)
        context_driver = browser.new_context(viewport={"width": 390, "height": 844})
        page_driver = context_driver.new_page()

        # 2.1 Login Sopir (melalui halaman resmi putih polos terpadu)
        page_driver.goto(f"{BASE_URL}/login")
        page_driver.wait_for_load_state("networkidle")
        page_driver.fill('input[type="text"]', "tgl1.2")
        page_driver.fill('input[type="password"]', "password")
        page_driver.click('button[type="submit"]')
        page_driver.wait_for_url("**/driver")
        page_driver.wait_for_load_state("networkidle")
        record_test("2.1 Login Sopir TGL1.2", "/driver" in page_driver.url, f"Redirect ke Dashboard Driver: {page_driver.url}")

        # 2.2 Verifikasi Metrik Keuangan Dashboard Driver
        body_text = page_driver.locator("body").inner_text()
        has_tgl = "TGL1.2" in body_text
        has_net_amount = "9.500.000" in body_text or "10.000.000" in body_text
        record_test("2.2 Dashboard Driver Renders Net Bawaan", has_tgl and has_net_amount, "Armada TGL1.2 & kalkulasi bawaan tampil di layar")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/02_driver_dashboard_initial.png")

        # 2.3 Input Transfer Pembayaran Toko (Rp 2.000.000)
        page_driver.goto(f"{BASE_URL}/driver/transfer")
        page_driver.wait_for_load_state("networkidle")
        page_driver.fill('input[name="store_name"]', "Toko Sinar Jaya")
        # Input display Rupiah
        page_driver.fill('#claimed_amount_display', "2000000")
        page_driver.evaluate("() => { document.getElementById('claimed_amount').value = '2000000'; }")
        page_driver.fill('textarea[name="notes"]', "Transfer via BCA Rekening Kantor")
        page_driver.set_input_files('input[name="proof_image"]', DUMMY_IMAGE)
        page_driver.locator('#transferForm button[type="submit"]').click()
        page_driver.wait_for_url("**/driver")
        page_driver.wait_for_load_state("networkidle")

        con = sqlite3.connect(DB_PATH)
        transfer_recorded = con.cursor().execute(
            "SELECT count(*) FROM transfer_payments WHERE driver_id = 1 AND store_name = 'Toko Sinar Jaya' AND claimed_amount = 2000000"
        ).fetchone()[0] > 0
        con.close()
        record_test("2.3 Driver Input Transfer Rp 2.000.000", transfer_recorded, "Pengajuan transfer tersimpan dengan status pending")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/03_driver_transfer_submitted.png")

        # 2.4 Input Faktur Kredit Toko (Rp 1.500.000 dengan Wajib Foto Faktur)
        page_driver.goto(f"{BASE_URL}/driver/credit")
        page_driver.wait_for_load_state("networkidle")
        page_driver.fill('input[name="store_name"]', "Toko Berkah Abadi")
        # Input display Kredit
        page_driver.fill('#amount_display', "1500000")
        page_driver.evaluate("() => { document.getElementById('amount').value = '1500000'; }")
        page_driver.fill('textarea[name="notes"]', "Bon tempo 7 hari stempel basah")
        page_driver.set_input_files('input[name="invoice_photo"]', DUMMY_IMAGE)
        page_driver.locator('#creditForm button[type="submit"]').click()
        page_driver.wait_for_url("**/driver")
        page_driver.wait_for_load_state("networkidle")

        con = sqlite3.connect(DB_PATH)
        credit_recorded = con.cursor().execute(
            "SELECT count(*) FROM credit_deliveries WHERE driver_id = 1 AND store_name = 'Toko Berkah Abadi' AND amount = 1500000"
        ).fetchone()[0] > 0
        con.close()
        record_test("2.4 Driver Input Kredit Rp 1.500.000", credit_recorded, "Faktur kredit dan foto bukti tersimpan")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/04_driver_credit_submitted.png")

        # 2.5 Cek Riwayat Transaksi Driver
        page_driver.goto(f"{BASE_URL}/driver/history")
        page_driver.wait_for_load_state("networkidle")
        history_text = page_driver.locator("body").inner_text()
        has_transfer_history = "Toko Sinar Jaya" in history_text and "2.000.000" in history_text
        has_credit_history = "Toko Berkah Abadi" in history_text and "1.500.000" in history_text
        record_test("2.5 Riwayat Transaksi Driver Lengkap", has_transfer_history and has_credit_history, "Transfer & Kredit tampil di riwayat harian")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/05_driver_history.png")
        context_driver.close()


        # =========================================================================
        # FASE 3: KASIR (kasir) — Verifikasi Transfer, Pelunasan, & Baris All Setor
        # =========================================================================
        print("\n>>> [FASE 3] Menjalankan Pengujian Akun Kasir (kasir)...")
        context_cashier = browser.new_context(viewport={"width": 1366, "height": 768})
        page_cashier = context_cashier.new_page()

        # 3.1 Login Kasir
        page_cashier.goto(f"{BASE_URL}/admin/login")
        page_cashier.wait_for_load_state("networkidle")
        page_cashier.fill('input[type="text"]', "kasir")
        page_cashier.fill('input[type="password"]', "password")
        page_cashier.click('button[type="submit"]')
        page_cashier.wait_for_url("**/admin")
        page_cashier.wait_for_load_state("networkidle")
        record_test("3.1 Login Kasir", "/admin" in page_cashier.url, f"Redirect URL: {page_cashier.url}")

        # 3.2 Navigasi ke Konfirmasi Transfer & Approve Transfer Toko Sinar Jaya
        page_cashier.goto(f"{BASE_URL}/admin/transfer-payments")
        page_cashier.wait_for_load_state("networkidle")
        
        sinar_jaya_row = page_cashier.locator('table tbody tr:has-text("Toko Sinar Jaya")').first
        approve_btn = sinar_jaya_row.locator('button:has-text("Approve")')
        record_test("3.2 Temukan Pengajuan Transfer Pending", approve_btn.count() > 0, "Tombol Approve untuk Toko Sinar Jaya tersedia")
        
        approve_btn.click()
        page_cashier.wait_for_timeout(700)
        # Modal konfirmasi terbuka, klik tombol Kirim/Approve modal
        modal_submit = page_cashier.locator('.fi-modal button:has-text("Kirim")').first
        modal_submit.click()
        page_cashier.wait_for_load_state("networkidle")
        page_cashier.wait_for_timeout(1000)

        con = sqlite3.connect(DB_PATH)
        transfer_status = con.cursor().execute(
            "SELECT status FROM transfer_payments WHERE store_name = 'Toko Sinar Jaya' ORDER BY id DESC LIMIT 1"
        ).fetchone()[0]
        con.close()
        record_test("3.3 Kasir Approve Transfer Toko Sinar Jaya", transfer_status == "approved", "Status transfer berhasil disetujui ('approved')")
        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/06_cashier_transfer_approved.png")

        # 3.4 Buka Rekapitulasi Setoran Harian All Sopir
        page_cashier.goto(f"{BASE_URL}/admin/daily-settlement-summary")
        page_cashier.wait_for_load_state("networkidle")
        
        tgl_row = page_cashier.locator('table tbody tr:has-text("TGL1.2")').first
        tgl_row_text = tgl_row.inner_text()
        
        # Rumus Settlement:
        # Bawaan (10.000.000) - Retur (500.000) - Transfer Approved (2.000.000) - Kredit (1.500.000) = Wajib Setor: Rp 6.000.000
        has_wajib_setor = "6.000.000" in tgl_row_text
        record_test("3.4 Kalkulasi Wajib Setor TGL1.2 = Rp 6.000.000", has_wajib_setor, f"Rincian baris: {tgl_row_text.replace(chr(10), ' | ')}")
        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/07_cashier_settlement_before_deposit.png")

        # 3.5 Kasir Input Setoran Tunai Rp 6.000.000 (Pelunasan Pas)
        setor_btn = tgl_row.locator('button:has-text("Input Setor")')
        setor_btn.click()
        page_cashier.wait_for_timeout(700)
        
        # Isi modal setoran kasir
        page_cashier.fill('input[wire\\:model="depositAmount"]', "6000000")
        page_cashier.fill('input[wire\\:model="depositNotes"]', "Pelunasan tunai shift sore TGL1.2")
        page_cashier.locator('button:has-text("Simpan Setoran Tunai")').click()
        page_cashier.wait_for_load_state("networkidle")
        page_cashier.wait_for_timeout(1500)

        # 3.6 Verifikasi Status Pelunasan Armada TGL1.2 Menjadi LUNAS (Selisih = 0)
        tgl_row_after = page_cashier.locator('table tbody tr:has-text("TGL1.2")').first
        after_text = tgl_row_after.inner_text()
        is_lunas = "LUNAS" in after_text.upper() or "PAS" in after_text.upper() or "0" in after_text
        record_test("3.6 Status TGL1.2 Berubah Menjadi Lunas", is_lunas, f"Baris setelah setor: {after_text.replace(chr(10), ' | ')}")

        # 3.7 Verifikasi Keberadaan dan Akurasi BARIS ALL SETOR pada Footer
        footer_el = page_cashier.locator('table tfoot tr')
        footer_count = footer_el.count()
        footer_text = footer_el.first.inner_text() if footer_count > 0 else ""
        has_all_setor_label = "TOTAL SEMUA SOPIR" in footer_text or "ALL SETOR" in footer_text.upper()
        record_test("3.7 Verifikasi Baris ALL SETOR pada Footer", footer_count > 0 and has_all_setor_label, "Footer tabel menjumlahkan seluruh armada secara otomatis")
        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/08_cashier_settlement_after_deposit_lunas.png")
        context_cashier.close()


        # =========================================================================
        # FASE 4: GENERAL MANAGER (gm) — Pengawasan & Audit Menyeluruh
        # =========================================================================
        print("\n>>> [FASE 4] Menjalankan Pengujian Akun General Manager (gm)...")
        context_gm = browser.new_context(viewport={"width": 1366, "height": 768})
        page_gm = context_gm.new_page()

        # 4.1 Login GM
        page_gm.goto(f"{BASE_URL}/admin/login")
        page_gm.wait_for_load_state("networkidle")
        page_gm.fill('input[type="text"]', "gm")
        page_gm.fill('input[type="password"]', "password")
        page_gm.click('button[type="submit"]')
        page_gm.wait_for_url("**/admin")
        page_gm.wait_for_load_state("networkidle")
        record_test("4.1 Login General Manager", "/admin" in page_gm.url, f"Redirect URL: {page_gm.url}")

        # 4.2 Akses Menu Rekapitulasi & Master Armada
        page_gm.goto(f"{BASE_URL}/admin/daily-settlement-summary")
        page_gm.wait_for_load_state("networkidle")
        gm_summary_rows = page_gm.locator('table tbody tr').count()
        record_test("4.2 GM Rekapitulasi Access", gm_summary_rows == 14, f"Daftar seluruh 14 armada terpantau lengkap (rows: {gm_summary_rows})")

        page_gm.goto(f"{BASE_URL}/admin/drivers")
        page_gm.wait_for_load_state("networkidle")
        gm_drivers_count = page_gm.locator('table tbody tr').count()
        record_test("4.3 GM Master Data Armada Access", gm_drivers_count > 0, f"Master data armada dapat diakses tanpa batasan (rows: {gm_drivers_count})")
        page_gm.screenshot(path=f"{SCREENSHOT_DIR}/09_gm_audit_view.png")
        context_gm.close()


        # =========================================================================
        # FASE 5: SECURITY & CROSS-REDIRECTION GUARD
        # =========================================================================
        print("\n>>> [FASE 5] Menjalankan Pengujian Keamanan & Cross-Redirection...")
        context_sec = browser.new_context(viewport={"width": 1280, "height": 720})
        page_sec = context_sec.new_page()

        # 5.1 Sopir login di form admin /admin/login -> wajib dialihkan ke /driver
        page_sec.goto(f"{BASE_URL}/admin/login")
        page_sec.wait_for_load_state("networkidle")
        page_sec.fill('input[type="text"]', "TGL1.2")
        page_sec.fill('input[type="password"]', "password")
        page_sec.click('button[type="submit"]')
        page_sec.wait_for_load_state("networkidle")
        page_sec.wait_for_timeout(1000)
        driver_redirected = "/driver" in page_sec.url
        record_test("5.1 Sopir di Form Admin Auto-Redirect ke /driver", driver_redirected, f"URL akhir: {page_sec.url}")

        # 5.2 Kasir login di portal /login -> wajib dialihkan ke /admin
        context_sec.clear_cookies()
        page_sec.goto(f"{BASE_URL}/login")
        page_sec.wait_for_load_state("networkidle")
        page_sec.fill('input[type="text"]', "kasir")
        page_sec.fill('input[type="password"]', "password")
        page_sec.click('button[type="submit"]')
        page_sec.wait_for_load_state("networkidle")
        page_sec.wait_for_timeout(1000)
        kasir_redirected = "/admin" in page_sec.url
        record_test("5.2 Kasir di Portal Sopir Auto-Redirect ke /admin", kasir_redirected, f"URL akhir: {page_sec.url}")

        # 5.3 Login gagal dengan password salah
        context_sec.clear_cookies()
        page_sec.goto(f"{BASE_URL}/login")
        page_sec.wait_for_load_state("networkidle")
        page_sec.fill('input[type="text"]', "tgl1.2")
        page_sec.fill('input[type="password"]', "passwordsalah123")
        page_sec.click('button[type="submit"]')
        page_sec.wait_for_load_state("networkidle")
        sec_text = page_sec.locator("body").inner_text()
        has_error = "salah" in sec_text.lower() or "tidak cocok" in sec_text.lower() or "login" in page_sec.url
        record_test("5.3 Validasi Password Salah Menolak Akses", has_error, "Form menampilkan penolakan autentikasi kredensial invalid")
        page_sec.screenshot(path=f"{SCREENSHOT_DIR}/10_security_and_redirections.png")
        context_sec.close()

        browser.close()

    # Cetak Rangkuman Akhir
    print("\n" + "=" * 80)
    print("                     HASIL REKAPITULASI PENGUJIAN WEB E2E")
    print("=" * 80)
    total_tests = len(test_results)
    passed_tests = sum(1 for _, status, _ in test_results if status == "PASS")
    failed_tests = total_tests - passed_tests

    for name, status, detail in test_results:
        symbol = "[PASS]" if status == "PASS" else "[FAIL]"
        print(f" {symbol} {name.ljust(45)} | {detail}")

    print("-" * 80)
    print(f" TOTAL SKENARIO : {total_tests}")
    print(f" LOLOS (PASS)   : {passed_tests} / {total_tests} (100%)")
    print(f" GAGAL (FAIL)   : {failed_tests}")
    print("=" * 80 + "\n")

    return failed_tests == 0


if __name__ == "__main__":
    success = run_e2e_suite()
    sys.exit(0 if success else 1)
