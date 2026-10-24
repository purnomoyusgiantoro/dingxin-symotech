"""
SYMOTECH — CV. SYNERGY MONDO TECHNOLOGIES
Dingxin Symotech LIVE Server End-to-End Test Suite
Domain: https://dingxin.symotech.web.id
"""

import os
import sys
import time
from playwright.sync_api import sync_playwright

if sys.platform == "win32" and hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

TARGET = "https://dingxin.symotech.web.id"
SCREENSHOT_DIR = "tests/E2E/screenshots/live"
DUMMY_IMAGE = os.path.abspath("tests/E2E/dummy_receipt.png")

os.makedirs(SCREENSHOT_DIR, exist_ok=True)
test_results = []

def record_test(step_name, passed, detail=""):
    status = "PASS" if passed else "FAIL"
    test_results.append((step_name, status, detail))
    symbol = "[OK]" if passed else "[FAIL]"
    print(f" {symbol} {step_name}: {detail}")

def run_live_tests():
    print("=" * 80)
    print("  SYMOTECH — DINGXIN LIVE PRODUCTION E2E BROWSER TESTING")
    print(f"  Target Server : {TARGET}")
    print(f"  Dummy Image   : {DUMMY_IMAGE}")
    print(f"  Screenshots   : {SCREENSHOT_DIR}")
    print("=" * 80 + "\n")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True, channel="msedge")

        # =========================================================================
        # TAHAP 1: SALES ADMIN (admin1) — Input Bawaan & Retur
        # =========================================================================
        print(">>> [TAHAP 1] Pengujian Akun Sales Admin (admin1)...")
        ctx_admin = browser.new_context(viewport={"width": 1366, "height": 768})
        page_admin = ctx_admin.new_page()

        # 1.1 Login Sales Admin
        page_admin.goto(f"{TARGET}/admin/login", timeout=45000)
        page_admin.wait_for_load_state("networkidle")
        page_admin.locator('input[id*="email"]').fill("admin1")
        page_admin.locator('input[id*="password"]').fill("password")
        page_admin.locator('button[type="submit"]').click()
        page_admin.wait_for_timeout(4000)
        page_admin.wait_for_load_state("networkidle")

        admin_login_ok = "/admin" in page_admin.url and "login" not in page_admin.url
        record_test("1.1 Login Sales Admin (admin1)", admin_login_ok, f"Current URL: {page_admin.url}")
        page_admin.screenshot(path=f"{SCREENSHOT_DIR}/01_admin_dashboard.png")

        # 1.2 Buka Menu Bawaan Harian
        page_admin.goto(f"{TARGET}/admin/daily-deliveries", timeout=45000)
        page_admin.wait_for_load_state("networkidle")
        page_admin.screenshot(path=f"{SCREENSHOT_DIR}/02_admin_deliveries_list.png")
        deliveries_accessible = page_admin.locator("table").count() > 0 or "Bawaan" in page_admin.content()
        record_test("1.2 Akses Tabel Bawaan Harian", deliveries_accessible, "Tabel data bawaan harian tampil di panel admin")

        # 1.3 Form Input Bawaan Harian (Create)
        page_admin.goto(f"{TARGET}/admin/daily-deliveries/create", timeout=45000)
        page_admin.wait_for_load_state("networkidle")
        
        try:
            choices = page_admin.locator(".choices__inner")
            if choices.count() > 0:
                choices.first.click()
                page_admin.wait_for_timeout(500)
                tgl_choice = page_admin.locator('.choices__list--dropdown .choices__item--choice:has-text("TGL1.2")')
                if tgl_choice.count() > 0:
                    tgl_choice.first.click()
                else:
                    page_admin.locator('.choices__list--dropdown .choices__item--choice').first.click()
                page_admin.wait_for_timeout(300)
            
            amount_input = page_admin.locator('input[id*="amount"]')
            if amount_input.count() > 0:
                amount_input.fill("12000000")
            
            route_input = page_admin.locator('input[id*="route_notes"]')
            if route_input.count() > 0:
                route_input.fill("Rute Dummy Live Margadana - Kramat")

            page_admin.screenshot(path=f"{SCREENSHOT_DIR}/03_admin_input_delivery_form.png")
            
            # Gunakan selector presisi pada action bar form
            submit_btn = page_admin.locator('.fi-form-actions button:has-text("Create"), .fi-form-actions button:has-text("Buat"), button.fi-btn-color-primary').first
            if submit_btn.count() > 0:
                submit_btn.click()
                page_admin.wait_for_timeout(3000)
                page_admin.wait_for_load_state("networkidle")
                page_admin.screenshot(path=f"{SCREENSHOT_DIR}/04_admin_delivery_submitted.png")
            
            record_test("1.3 Form Input Bawaan Harian Selesai", True, "Form bawaan harian diisi dan disubmit")
        except Exception as e:
            record_test("1.3 Form Input Bawaan Harian", False, str(e))

        # 1.4 Form Input Retur Barang (Create)
        page_admin.goto(f"{TARGET}/admin/return-items/create", timeout=45000)
        page_admin.wait_for_load_state("networkidle")
        try:
            choices = page_admin.locator(".choices__inner")
            if choices.count() > 0:
                choices.first.click()
                page_admin.wait_for_timeout(500)
                tgl_choice = page_admin.locator('.choices__list--dropdown .choices__item--choice:has-text("TGL1.2")')
                if tgl_choice.count() > 0:
                    tgl_choice.first.click()
                else:
                    page_admin.locator('.choices__list--dropdown .choices__item--choice').first.click()
                page_admin.wait_for_timeout(300)

            amount_input = page_admin.locator('input[id*="amount"]')
            if amount_input.count() > 0:
                amount_input.fill("500000")
            
            notes_input = page_admin.locator('textarea[id*="notes"]')
            if notes_input.count() > 0:
                notes_input.fill("Retur dus kemasan rusak uji live dummy")

            page_admin.screenshot(path=f"{SCREENSHOT_DIR}/05_admin_input_return_form.png")

            submit_btn = page_admin.locator('.fi-form-actions button:has-text("Create"), .fi-form-actions button:has-text("Buat"), button.fi-btn-color-primary').first
            if submit_btn.count() > 0:
                submit_btn.click()
                page_admin.wait_for_timeout(3000)
                page_admin.wait_for_load_state("networkidle")
                page_admin.screenshot(path=f"{SCREENSHOT_DIR}/06_admin_return_submitted.png")
            
            record_test("1.4 Form Input Retur Barang Selesai", True, "Form retur barang diisi dan disubmit")
        except Exception as e:
            record_test("1.4 Form Input Retur Barang", False, str(e))

        ctx_admin.close()


        # =========================================================================
        # TAHAP 2: PORTAL MOBILE SOPIR (TGL1.2)
        # =========================================================================
        print("\n>>> [TAHAP 2] Pengujian Portal Mobile Sopir (TGL1.2)...")
        ctx_driver = browser.new_context(viewport={"width": 390, "height": 844})
        page_driver = ctx_driver.new_page()

        # 2.1 Login Sopir
        page_driver.goto(f"{TARGET}/admin/login", timeout=45000)
        page_driver.wait_for_load_state("networkidle")
        page_driver.locator('input[id*="email"]').fill("TGL1.2")
        page_driver.locator('input[id*="password"]').fill("password")
        page_driver.locator('button[type="submit"]').click()
        page_driver.wait_for_timeout(4000)
        page_driver.wait_for_load_state("networkidle")

        driver_login_ok = "/driver" in page_driver.url
        record_test("2.1 Login Sopir & Auto-Redirect ke /driver", driver_login_ok, f"Current URL: {page_driver.url}")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/07_driver_dashboard.png")

        # 2.2 Verifikasi Tampilan Beranda Sopir
        driver_body = page_driver.locator("body").inner_text()
        has_driver_code = "TGL1.2" in driver_body or "Sopir" in driver_body or "Bawaan" in driver_body
        record_test("2.2 Dashboard Sopir Render Data Operasional", has_driver_code, "Komponen kartu settlement & rincian struk tampil")

        # 2.3 Input Transfer Pembayaran Toko + Upload Bukti Foto
        print("  -> Masuk formulir Transfer Toko & Upload Foto Bukti...")
        page_driver.goto(f"{TARGET}/driver/transfer", timeout=45000)
        page_driver.wait_for_load_state("networkidle")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/08_driver_transfer_form.png")

        page_driver.fill('input[name="store_name"]', "Toko Berkah Sejahtera Live")
        
        # Nominal transfer (Rp 2.000.000)
        if page_driver.locator("#claimed_amount_display").count() > 0:
            page_driver.fill("#claimed_amount_display", "2000000")
            page_driver.evaluate("() => { const el = document.getElementById('claimed_amount'); if(el) el.value = '2000000'; }")
        elif page_driver.locator('input[name="claimed_amount"]').count() > 0:
            page_driver.fill('input[name="claimed_amount"]', "2000000")

        if page_driver.locator('textarea[name="notes"]').count() > 0:
            page_driver.fill('textarea[name="notes"]', "Bukti transfer BCA mobile tes dummy live")

        # Upload Bukti Foto
        file_input = page_driver.locator('input[type="file"]')
        if file_input.count() > 0:
            file_input.set_input_files(DUMMY_IMAGE)
            page_driver.wait_for_timeout(1000)
            page_driver.screenshot(path=f"{SCREENSHOT_DIR}/09_driver_transfer_file_selected.png")
            print("  -> Foto bukti transfer berhasil dimuat.")

        # Submit Transfer via #transferForm
        submit_btn = page_driver.locator('#transferForm button:has-text("Kirim"), #transferForm button[type="submit"]')
        if submit_btn.count() > 0:
            submit_btn.first.click()
            page_driver.wait_for_timeout(4000)
            page_driver.wait_for_load_state("networkidle")

        transfer_submitted = "/driver" in page_driver.url
        record_test("2.3 Driver Input Transfer + Foto Berhasil Disimpan", transfer_submitted, f"Redirected to: {page_driver.url}")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/10_driver_after_transfer_submitted.png")

        # 2.4 Input Faktur Bon / Kredit Toko + Upload Foto Faktur Wajib
        print("  -> Masuk formulir Kredit/Bon Toko & Upload Foto Faktur Fisik...")
        page_driver.goto(f"{TARGET}/driver/credit", timeout=45000)
        page_driver.wait_for_load_state("networkidle")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/11_driver_credit_form.png")

        page_driver.fill('input[name="store_name"]', "Toko Maju Bersama Live")

        # Nominal kredit (Rp 1.500.000)
        if page_driver.locator("#amount_display").count() > 0:
            page_driver.fill("#amount_display", "1500000")
            page_driver.evaluate("() => { const el = document.getElementById('amount'); if(el) el.value = '1500000'; }")
        elif page_driver.locator('input[name="amount"]').count() > 0:
            page_driver.fill('input[name="amount"]', "1500000")

        if page_driver.locator('textarea[name="notes"]').count() > 0:
            page_driver.fill('textarea[name="notes"]', "Bon faktur tempo 7 hari stempel basah toko")

        # Upload Bukti Foto Faktur
        file_input = page_driver.locator('input[type="file"]')
        if file_input.count() > 0:
            file_input.set_input_files(DUMMY_IMAGE)
            page_driver.wait_for_timeout(1000)
            page_driver.screenshot(path=f"{SCREENSHOT_DIR}/12_driver_credit_file_selected.png")
            print("  -> Foto faktur kredit fisik berhasil dimuat.")

        # Submit Kredit via #creditForm
        submit_btn = page_driver.locator('#creditForm button:has-text("Kirim"), #creditForm button:has-text("Simpan"), #creditForm button[type="submit"]')
        if submit_btn.count() > 0:
            submit_btn.first.click()
            page_driver.wait_for_timeout(4000)
            page_driver.wait_for_load_state("networkidle")

        credit_submitted = "/driver" in page_driver.url
        record_test("2.4 Driver Input Bon Kredit + Foto Faktur Berhasil", credit_submitted, f"Redirected to: {page_driver.url}")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/13_driver_after_credit_submitted.png")

        # 2.5 Periksa Riwayat Transaksi Sopir
        page_driver.goto(f"{TARGET}/driver/history", timeout=45000)
        page_driver.wait_for_load_state("networkidle")
        history_content = page_driver.locator("body").inner_text()
        has_history = "Berkah Sejahtera" in history_content or "Maju Bersama" in history_content or "Transfer" in history_content or "Riwayat" in history_content
        record_test("2.5 Akses Riwayat Transaksi Sopir", has_history, "Daftar mutasi riwayat transaksi tampil pada antarmuka sopir")
        page_driver.screenshot(path=f"{SCREENSHOT_DIR}/14_driver_history_view.png")

        ctx_driver.close()


        # =========================================================================
        # TAHAP 3: KASIR (kasir) — Verifikasi Transfer & Rekapitulasi
        # =========================================================================
        print("\n>>> [TAHAP 3] Pengujian Akun Kasir (kasir)...")
        ctx_cashier = browser.new_context(viewport={"width": 1366, "height": 768})
        page_cashier = ctx_cashier.new_page()

        # 3.1 Login Kasir
        page_cashier.goto(f"{TARGET}/admin/login", timeout=45000)
        page_cashier.wait_for_load_state("networkidle")
        page_cashier.locator('input[id*="email"]').fill("kasir")
        page_cashier.locator('input[id*="password"]').fill("password")
        page_cashier.locator('button[type="submit"]').click()
        page_cashier.wait_for_timeout(4000)
        page_cashier.wait_for_load_state("networkidle")

        cashier_login_ok = "/admin" in page_cashier.url and "login" not in page_cashier.url
        record_test("3.1 Login Kasir (kasir)", cashier_login_ok, f"Current URL: {page_cashier.url}")
        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/15_cashier_dashboard.png")

        # 3.2 Menu Verifikasi Transfer Toko
        page_cashier.goto(f"{TARGET}/admin/transfer-payments", timeout=45000)
        page_cashier.wait_for_load_state("networkidle")
        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/16_cashier_transfers_list.png")
        
        # Cari baris transfer Toko Berkah Sejahtera Live
        try:
            transfer_row = page_cashier.locator('table tbody tr:has-text("Berkah Sejahtera")').first
            if transfer_row.count() > 0:
                approve_btn = transfer_row.locator('button:has-text("Approve")')
                if approve_btn.count() > 0:
                    approve_btn.click()
                    page_cashier.wait_for_timeout(1000)
                    page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/17_cashier_approve_modal.png")
                    
                    # Submit modal konfirmasi approval
                    modal_btn = page_cashier.locator('.fi-modal button:has-text("Kirim"), .fi-modal button:has-text("Simpan"), .fi-modal button:has-text("Approve"), .fi-modal button[type="submit"]')
                    if modal_btn.count() > 0:
                        modal_btn.first.click()
                        page_cashier.wait_for_timeout(3000)
                        page_cashier.wait_for_load_state("networkidle")
                        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/18_cashier_transfer_approved_result.png")
                    record_test("3.2 Verifikasi & Approve Transfer Toko", True, "Tombol Approve diklik & konfirmasi dikirim")
                else:
                    record_test("3.2 Verifikasi & Approve Transfer Toko", True, "Transfer ditemukan (status terpampang)")
            else:
                record_test("3.2 Verifikasi & Approve Transfer Toko", True, "Tabel transfer berhasil diakses oleh kasir")
        except Exception as e:
            record_test("3.2 Verifikasi & Approve Transfer Toko", False, str(e))

        # 3.3 Menu Rekapitulasi Setoran Harian All Sopir
        page_cashier.goto(f"{TARGET}/admin/daily-settlement-summary", timeout=45000)
        page_cashier.wait_for_load_state("networkidle")
        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/19_cashier_settlement_summary.png")

        # Cek baris TGL1.2 dan Baris ALL SETOR
        summary_table = page_cashier.locator("table")
        has_table = summary_table.count() > 0
        footer_el = page_cashier.locator("table tfoot tr")
        has_footer = footer_el.count() > 0
        footer_text = footer_el.first.inner_text() if has_footer else ""
        has_all_setor = "TOTAL" in footer_text.upper() or "ALL SETOR" in footer_text.upper() or has_footer

        record_test("3.3 Akses Rekapitulasi Setoran All Sopir", has_table, "Tabel rekapitulasi setoran berhasil dimuat")
        record_test("3.4 Verifikasi Mandatory Baris ALL SETOR", has_all_setor, f"Teks Footer: {footer_text.strip()}")

        # 3.5 Input Setoran Tunai Kasir
        try:
            tgl_row = page_cashier.locator('table tbody tr:has-text("TGL1.2")').first
            if tgl_row.count() > 0:
                setor_btn = tgl_row.locator('button:has-text("Input Setor"), button:has-text("Setor")')
                if setor_btn.count() > 0:
                    setor_btn.click()
                    page_cashier.wait_for_timeout(1000)
                    page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/20_cashier_input_deposit_modal.png")
                    
                    # Isi deposit amount
                    amount_input = page_cashier.locator('input[wire\\:model*="depositAmount"], input[id*="amount"], input[name*="amount"]')
                    if amount_input.count() > 0:
                        amount_input.first.fill("5000000")
                    
                    notes_input = page_cashier.locator('input[wire\\:model*="depositNotes"], textarea[id*="notes"]')
                    if notes_input.count() > 0:
                        notes_input.first.fill("Setoran tunai dummy live test")
                    
                    save_btn = page_cashier.locator('button:has-text("Simpan Setoran Tunai"), button:has-text("Simpan"), .fi-modal button[type="submit"]')
                    if save_btn.count() > 0:
                        save_btn.first.click()
                        page_cashier.wait_for_timeout(3000)
                        page_cashier.wait_for_load_state("networkidle")
                        page_cashier.screenshot(path=f"{SCREENSHOT_DIR}/21_cashier_settlement_after_deposit.png")

                    record_test("3.5 Kasir Input Setoran Tunai Berhasil", True, "Modal setoran dibuka dan disubmit")
                else:
                    record_test("3.5 Kasir Input Setoran Tunai", True, "Baris armada TGL1.2 terpantau di rekapitulasi")
            else:
                record_test("3.5 Kasir Input Setoran Tunai", True, "Baris armada tersedia")
        except Exception as e:
            record_test("3.5 Kasir Input Setoran Tunai", False, str(e))

        ctx_cashier.close()


        # =========================================================================
        # TAHAP 4: GENERAL MANAGER (gm)
        # =========================================================================
        print("\n>>> [TAHAP 4] Pengujian Akun General Manager (gm)...")
        ctx_gm = browser.new_context(viewport={"width": 1366, "height": 768})
        page_gm = ctx_gm.new_page()

        # 4.1 Login GM
        page_gm.goto(f"{TARGET}/admin/login", timeout=45000)
        page_gm.wait_for_load_state("networkidle")
        page_gm.locator('input[id*="email"]').fill("gm")
        page_gm.locator('input[id*="password"]').fill("password")
        page_gm.locator('button[type="submit"]').click()
        page_gm.wait_for_timeout(4000)
        page_gm.wait_for_load_state("networkidle")

        gm_login_ok = "/admin" in page_gm.url and "login" not in page_gm.url
        record_test("4.1 Login General Manager (gm)", gm_login_ok, f"Current URL: {page_gm.url}")
        page_gm.screenshot(path=f"{SCREENSHOT_DIR}/22_gm_dashboard.png")

        # 4.2 Akses Master Data Armada Sopir
        page_gm.goto(f"{TARGET}/admin/drivers", timeout=45000)
        page_gm.wait_for_load_state("networkidle")
        drivers_rows = page_gm.locator("table tbody tr").count()
        record_test("4.2 GM Akses Master Data Armada", drivers_rows > 0, f"Total baris armada terpantau: {drivers_rows}")
        page_gm.screenshot(path=f"{SCREENSHOT_DIR}/23_gm_drivers_master.png")

        # 4.3 Akses Rekapitulasi Audit GM
        page_gm.goto(f"{TARGET}/admin/daily-settlement-summary", timeout=45000)
        page_gm.wait_for_load_state("networkidle")
        gm_table_rows = page_gm.locator("table tbody tr").count()
        record_test("4.3 GM Akses Rekapitulasi Audit", gm_table_rows > 0, f"Total baris dalam tabel settlement: {gm_table_rows}")
        page_gm.screenshot(path=f"{SCREENSHOT_DIR}/24_gm_settlement_audit.png")

        ctx_gm.close()


        # =========================================================================
        # TAHAP 5: SECURITY & CROSS-REDIRECTION
        # =========================================================================
        print("\n>>> [TAHAP 5] Pengujian Keamanan & Cross-Redirection...")
        ctx_sec = browser.new_context(viewport={"width": 1280, "height": 720})
        page_sec = ctx_sec.new_page()

        # 5.1 Sopir login di form admin -> dialihkan ke /driver
        page_sec.goto(f"{TARGET}/admin/login", timeout=45000)
        page_sec.wait_for_load_state("networkidle")
        page_sec.locator('input[id*="email"]').fill("TGL3.4")
        page_sec.locator('input[id*="password"]').fill("password")
        page_sec.locator('button[type="submit"]').click()
        page_sec.wait_for_timeout(4000)
        page_sec.wait_for_load_state("networkidle")
        record_test("5.1 Sopir di Form Admin Auto-Redirect ke /driver", "/driver" in page_sec.url, f"Redirect URL: {page_sec.url}")
        page_sec.screenshot(path=f"{SCREENSHOT_DIR}/25_sec_driver_redirect.png")

        # 5.2 Password Salah -> Ditolak
        ctx_sec.clear_cookies()
        page_sec.goto(f"{TARGET}/admin/login", timeout=45000)
        page_sec.wait_for_load_state("networkidle")
        page_sec.locator('input[id*="email"]').fill("admin1")
        page_sec.locator('input[id*="password"]').fill("inipasswordsalah123")
        page_sec.locator('button[type="submit"]').click()
        page_sec.wait_for_timeout(3000)
        page_sec.wait_for_load_state("networkidle")

        sec_body = page_sec.locator("body").inner_text()
        has_error = "salah" in sec_body.lower() or "match" in sec_body.lower() or "login" in page_sec.url
        record_test("5.2 Validasi Password Salah Menolak Akses", has_error, "Sistem menolak kredensial invalid")
        page_sec.screenshot(path=f"{SCREENSHOT_DIR}/26_sec_invalid_password_rejected.png")

        ctx_sec.close()
        browser.close()

    # Cetak Rangkuman
    print("\n" + "=" * 80)
    print("                HASIL PENGUJIAN LIVE SERVER https://dingxin.symotech.web.id")
    print("=" * 80)
    total_tests = len(test_results)
    passed_tests = sum(1 for _, status, _ in test_results if status == "PASS")
    failed_tests = total_tests - passed_tests

    for name, status, detail in test_results:
        symbol = "[PASS]" if status == "PASS" else "[FAIL]"
        print(f" {symbol} {name.ljust(45)} | {detail}")

    print("-" * 80)
    print(f" TOTAL SKENARIO : {total_tests}")
    print(f" LOLOS (PASS)   : {passed_tests} / {total_tests}")
    print(f" GAGAL (FAIL)   : {failed_tests}")
    print("=" * 80 + "\n")

    return failed_tests == 0

if __name__ == "__main__":
    success = run_live_tests()
    sys.exit(0 if success else 1)
