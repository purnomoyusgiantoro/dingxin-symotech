import sys
from playwright.sync_api import sync_playwright

if sys.platform == "win32" and hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")

TARGET = "https://dingxin.symotech.web.id"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, channel="msedge")
    page = browser.new_page(viewport={"width": 1366, "height": 768})
    print(f"Connecting to {TARGET}/admin/login...")
    page.goto(f"{TARGET}/admin/login", timeout=30000)
    page.wait_for_load_state("networkidle")
    print("Page Title:", page.title())
    print("Page URL:", page.url)

    inputs = page.locator("input").all()
    for inp in inputs:
        print("Input id:", inp.get_attribute("id"), "name:", inp.get_attribute("name"), "type:", inp.get_attribute("type"))

    buttons = page.locator("button").all()
    for btn in buttons:
        print("Button text:", btn.inner_text().strip(), "type:", btn.get_attribute("type"))

    page.screenshot(path="tests/E2E/screenshots/live_admin_login_probe.png")
    print("Screenshot saved to tests/E2E/screenshots/live_admin_login_probe.png")
    browser.close()
