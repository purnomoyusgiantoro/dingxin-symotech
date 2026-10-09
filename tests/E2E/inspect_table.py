from playwright.sync_api import sync_playwright
import json

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, channel='msedge')
    page = browser.new_page(viewport={'width': 1366, 'height': 768})
    page.goto('http://127.0.0.1:8000/admin/login')
    page.fill('input[type="text"]', 'kasir')
    page.fill('input[type="password"]', 'password')
    page.click('button[type="submit"]')
    page.wait_for_url('**/admin')
    page.goto('http://127.0.0.1:8000/admin/daily-settlement-summary')
    page.wait_for_load_state('networkidle')

    info = page.evaluate('''() => {
        const table = document.querySelector('table');
        const container = table ? table.parentElement : null;
        const main = document.querySelector('.fi-main') || document.body;
        return {
            windowWidth: window.innerWidth,
            tableWidth: table ? table.offsetWidth : 0,
            tableScrollWidth: table ? table.scrollWidth : 0,
            containerWidth: container ? container.offsetWidth : 0,
            containerScrollWidth: container ? container.scrollWidth : 0,
            containerOverflowX: container ? window.getComputedStyle(container).overflowX : '',
            headers: Array.from(document.querySelectorAll('table th')).map(th => ({ text: th.innerText.replace(/\\n/g, ' '), width: th.offsetWidth })),
            rowsCount: document.querySelectorAll('table tbody tr').length
        };
    }''')
    print(json.dumps(info, indent=2))
    browser.close()
