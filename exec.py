#!/usr/bin/env python3
import sys
import json
import time
import os
import traceback

def bypass_and_get_cookies(target_url):
    try:
        from playwright.sync_api import sync_playwright
    except ImportError:
        return {"cf_clearance": "", "user_agent": "", "error": "playwright not installed"}

    try:
        with sync_playwright() as p:
            browser = p.chromium.launch(
                headless=True,
                args=[
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--disable-web-security',
                    '--disable-features=IsolateOrigins,site-per-process',
                    '--disable-blink-features=AutomationControlled',
                    '--single-process',
                ]
            )

            context = browser.new_context(
                viewport={"width": 390, "height": 844},
                user_agent="Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36",
                locale="id-ID",
                timezone_id="Asia/Jakarta"
            )

            page = context.new_page()

            try:
                page.add_init_script("""
                    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
                    Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3, 4, 5] });
                    Object.defineProperty(navigator, 'languages', { get: () => ['id-ID', 'id', 'en-US', 'en'] });
                    Object.defineProperty(navigator, 'platform', { get: () => 'Linux armv8l' });
                """)

                page.goto(target_url, wait_until="domcontentloaded", timeout=45000)
                time.sleep(5)

                for _ in range(45):
                    text = page.evaluate("() => document.body.innerText")
                    if "Just a moment" not in text:
                        break
                    time.sleep(2)

                time.sleep(2)

                cookies = context.cookies()
                cf_value = ""
                for c in cookies:
                    if c['name'] == 'cf_clearance':
                        cf_value = c['value']
                        break

                ua = page.evaluate("() => navigator.userAgent")
                browser.close()

                return {
                    "cf_clearance": f"cf_clearance={cf_value}" if cf_value else "",
                    "user_agent": ua
                }

            except Exception as e:
                browser.close()
                return {"cf_clearance": "", "user_agent": "", "error": traceback.format_exc()}

    except Exception as e:
        return {"cf_clearance": "", "user_agent": "", "error": f"browser launch failed: {str(e)}"}


def get_fresh_cookies(target_url):
    try:
        from playwright.sync_api import sync_playwright
    except ImportError:
        return {"error": "playwright not installed"}

    try:
        with sync_playwright() as p:
            browser = p.chromium.launch(
                headless=True,
                args=[
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--disable-blink-features=AutomationControlled',
                    '--single-process',
                ]
            )

            context = browser.new_context(
                viewport={"width": 390, "height": 844},
                locale="id-ID",
                timezone_id="Asia/Jakarta"
            )

            page = context.new_page()

            try:
                page.add_init_script("""
                    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
                    Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3, 4, 5] });
                """)

                page.goto(target_url, wait_until="domcontentloaded", timeout=45000)
                time.sleep(5)

                for _ in range(45):
                    text = page.evaluate("() => document.body.innerText")
                    if "Just a moment" not in text:
                        break
                    time.sleep(2)

                time.sleep(2)

                cookies = context.cookies()
                cookie_str = "; ".join([f"{c['name']}={c['value']}" for c in cookies])
                ua = page.evaluate("() => navigator.userAgent")
                browser.close()

                return {"cookie": cookie_str, "user_agent": ua}

            except Exception as e:
                browser.close()
                return {"error": traceback.format_exc()}

    except Exception as e:
        return {"error": f"browser launch failed: {str(e)}"}


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No URL provided"}))
        sys.exit(1)

    target = sys.argv[1]

    if target == "--get-cookies" and len(sys.argv) >= 3:
        result = get_fresh_cookies(sys.argv[2])
    else:
        result = bypass_and_get_cookies(target)

    print(json.dumps(result))
