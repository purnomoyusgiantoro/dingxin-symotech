import sys
from playwright.sync_api import sync_playwright

if sys.platform == "win32" and hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

TARGET = "https://dingxin.symotech.web.id"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, channel="msedge")
    page = browser.new_page(viewport={"width": 1366, "height": 768})

    # 1. Login Kasir untuk verifikasi data
    page.goto(f"{TARGET}/admin/login", timeout=45000)
    page.wait_for_load_state("networkidle")
    page.locator('input[id*="email"]').fill("kasir")
    page.locator('input[id*="password"]').fill("password")
    page.locator('button[type="submit"]').click()
    page.wait_for_timeout(4000)
    page.wait_for_load_state("networkidle")

    # 2. Cek Konfirmasi Transfer
    page.goto(f"{TARGET}/admin/transfer-payments", timeout=45000)
    page.wait_for_load_state("networkidle")
    rows = page.locator("table tbody tr").all()
    print("=== STATUS TABEL TRANSFER PAYMENTS ===")
    print(f"Total baris transfer: {len(rows)}")
    for idx, r in enumerate(rows[:5]):
        txt = r.inner_text().replace("\n", " | ")
        print(f"[{idx+1}] {txt}")

    # 3. Cek Foto Bukti Transfer di Storage
    img_elements = page.locator('table a, table img').all()
    for el in img_elements:
        href = el.get_attribute("href") or el.get_attribute("src")
        if href and ("storage" in href or "transfer" in href):
            print(f"Link Foto Bukti: {href}")

    # 4. Cek Rekapitulasi Setoran All Sopir
    page.goto(f"{TARGET}/admin/daily-settlement-summary", timeout=45000)
    page.wait_for_load_state("networkidle")
    print("\n=== STATUS REKAPITULASI SETORAN ALL SOPIR ===")
    tgl_row = page.locator('table tbody tr:has-text("TGL1.2")').first
    if tgl_row.count() > 0:
        print("Baris TGL1.2:")
        print(tgl_row.inner_text().replace("\n", " | "))

    footer = page.locator("table tfoot tr").first
    if footer.count() > 0:
        print("\nBaris Footer All Setor:")
        print(footer.inner_text().replace("\n", " | "))

    # 5. Cek Rincian Modal Setoran (Klik tombol Input Setor / Rincian)
    print("\n=== PEMERIKSAAN MODAL SETORAN & LOG CASHIER ===")
    if tgl_row.count() > 0:
        setor_btn = tgl_row.locator('button:has-text("Input Setor")')
        if setor_btn.count() > 0:
            setor_btn.click()
            page.wait_for_timeout(1500)
            modal_text = page.locator(".fi-modal").inner_text()
            print("Konten Modal Setoran:")
            print(modal_text.replace("\n", " | ")[:300])

    browser.close()
