from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, channel="msedge")
    page = browser.new_page(viewport={'width': 1366, 'height': 768})

    page.goto('http://127.0.0.1:8000/admin/login')
    page.wait_for_load_state('networkidle')
    page.fill('input[type="text"]', 'kasir')
    page.fill('input[type="password"]', 'password')
    page.click('button[type="submit"]')
    page.wait_for_url('**/admin')

    page.goto('http://127.0.0.1:8000/admin/daily-settlement-summary')
    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(1000)

    # 1. Capture unscrolled at 1366
    page.screenshot(path='tests/E2E/screenshots/kasir_table_debug_1366.png', full_page=True)

    # 2. Scroll the table horizontally to the far right
    page.evaluate('''() => {
        const container = document.querySelector('.settlement-scroll-container');
        if (container) {
            container.scrollLeft = container.scrollWidth;
        }
    }''')
    page.wait_for_timeout(500)
    page.screenshot(path='tests/E2E/screenshots/kasir_table_scrolled_right_1366.png', full_page=True)

    # 3. Click "+ Input Setor" on the first row
    btn = page.locator('button:has-text("Input Setor")').first
    if btn.is_visible():
        btn.click()
        page.wait_for_timeout(800)
        page.screenshot(path='tests/E2E/screenshots/kasir_modal_check.png')
        print("Captured modal screenshot")

    browser.close()
