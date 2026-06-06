#!/usr/bin/env python3
import sys
import json
import traceback

def bypass_and_get_cookies(target_url):
    try:
        import cloudscraper
    except ImportError as e:
        return {"cf_clearance": "", "user_agent": "", "error": f"cloudscraper: {e}"}

    try:
        scraper = cloudscraper.create_scraper(
            browser={'browser': 'chrome', 'platform': 'android', 'mobile': True},
            delay=5
        )

        # Make a GET with detailed response info
        resp = scraper.get(target_url, timeout=30, allow_redirects=True)

        # Check raw response headers for Set-Cookie
        set_cookies = resp.raw._original_response.headers.get_all('Set-Cookie') if hasattr(resp.raw, '_original_response') and resp.raw._original_response else []
        if not set_cookies:
            set_cookies = resp.headers.get_all('Set-Cookie') if hasattr(resp.headers, 'get_all') else []
        if not set_cookies and 'Set-Cookie' in resp.headers:
            set_cookies = [resp.headers['Set-Cookie']]

        # Get all cookies from the session
        all_cookies = {}
        for c in scraper.cookies:
            all_cookies[c.name] = {"value": c.value, "domain": c.domain, "path": c.path}

        cf_value = all_cookies.get('cf_clearance', {}).get('value', '')

        body_preview = resp.text[:300] if resp.text else ''
        has_justmoment = 'Just a moment' in resp.text
        has_costranchill = 'costranchill.com' in resp.text

        return {
            "cf_clearance": f"cf_clearance={cf_value}" if cf_value else "",
            "user_agent": "Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/125.0.0.0 Mobile Safari/537.36",
            "status": resp.status_code,
            "cookies": list(all_cookies.keys()),
            "set_cookie_headers": set_cookies[:5],
            "has_justmoment": has_justmoment,
            "has_costranchill": has_costranchill,
            "body_preview": body_preview
        }

    except Exception as e:
        return {"cf_clearance": "", "user_agent": "", "error": traceback.format_exc()}


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No URL provided"}))
        sys.exit(1)
    result = bypass_and_get_cookies(sys.argv[1])
    print(json.dumps(result))
