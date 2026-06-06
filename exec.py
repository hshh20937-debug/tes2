#!/usr/bin/env python3
import sys
import json
import traceback

def bypass_and_get_cookies(target_url):
    try:
        import cloudscraper
    except ImportError as e:
        return {"cf_clearance": "", "user_agent": "", "error": f"cloudscraper not installed: {e}"}

    try:
        scraper = cloudscraper.create_scraper(
            browser={
                'browser': 'chrome',
                'platform': 'android',
                'mobile': True,
            },
            delay=5
        )
        resp = scraper.get(target_url, timeout=30, allow_redirects=True)

        all_cookies = {c.name: c.value for c in scraper.cookies}
        cf_value = all_cookies.get('cf_clearance', '')

        body_preview = resp.text[:200] if resp.text else ''
        has_justmoment = 'Just a moment' in resp.text
        has_challenge = 'challenges.cloudflare' in resp.text

        return {
            "cf_clearance": f"cf_clearance={cf_value}" if cf_value else "",
            "user_agent": "Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 Chrome/125.0.0.0 Mobile Safari/537.36",
            "status": resp.status_code,
            "cookies_found": list(all_cookies.keys()),
            "has_justmoment": has_justmoment,
            "has_challenge": has_challenge,
            "body_preview": body_preview
        }
    except Exception as e:
        return {"cf_clearance": "", "user_agent": "", "error": traceback.format_exc()}


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No URL provided"}))
        sys.exit(1)
    target = sys.argv[1]
    result = bypass_and_get_cookies(target)
    print(json.dumps(result))
