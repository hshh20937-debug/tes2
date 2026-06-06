#!/usr/bin/env python3
import sys
import json
import time

def bypass_and_get_cookies(target_url):
    try:
        import cloudscraper

        scraper = cloudscraper.create_scraper(
            browser={
                'browser': 'chrome',
                'platform': 'android',
                'mobile': True,
            }
        )

        resp = scraper.get(target_url, timeout=60)
        cf_value = ""
        for c in scraper.cookies:
            if c.name == 'cf_clearance':
                cf_value = c.value
                break

        return {
            "cf_clearance": f"cf_clearance={cf_value}" if cf_value else "",
            "user_agent": "Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36"
        }

    except Exception as e:
        return {"cf_clearance": "", "user_agent": "", "error": f"cloudscraper: {str(e)}"}


def get_fresh_cookies(target_url):
    try:
        import cloudscraper

        scraper = cloudscraper.create_scraper(
            browser={
                'browser': 'chrome',
                'platform': 'android',
                'mobile': True,
            }
        )

        resp = scraper.get(target_url, timeout=60)

        cookie_str = "; ".join([f"{c.name}={c.value}" for c in scraper.cookies])
        ua = "Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36"

        return {"cookie": cookie_str, "user_agent": ua}

    except Exception as e:
        return {"error": f"cloudscraper: {str(e)}"}


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
