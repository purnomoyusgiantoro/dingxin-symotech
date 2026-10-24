from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, channel="msedge")
    page = browser.new_page()
    page.goto("http://127.0.0.1:8000/admin/login")
    page.wait_for_load_state("networkidle")
    page.screenshot(path="tests/E2E/screenshots/debug_admin_login_page.png")
    print("Initial URL:", page.url)

    inputs = page.locator("input").all()
    for inp in inputs:
        print("Input id:", inp.get_attribute("id"), "name:", inp.get_attribute("name"), "type:", inp.get_attribute("type"))

    buttons = page.locator("button").all()
    for btn in buttons:
        print("Button text:", btn.inner_text().strip(), "type:", btn.get_attribute("type"))

    # Fill username
    first_text = page.locator('input[type="text"], input[type="email"], input[id*="email"], input[id*="login"]').first
    print("Filling:", first_text.get_attribute("id"))
    first_text.fill("admin1")
    
    pwd = page.locator('input[type="password"]').first
    pwd.fill("password")
    
    page.locator('button[type="submit"]').click()
    page.wait_for_timeout(3000)
    print("After submit URL:", page.url)
    page.screenshot(path="tests/E2E/screenshots/debug_admin_after_submit.png")
    print("Page text snippet:", repr(page.locator("body").inner_text()[:300]))
    browser.close()
