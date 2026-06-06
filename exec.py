#!/usr/bin/env python3
import sys
import json
import re
import time
import traceback

def bypass_and_get_cookies(target_url):
    try:
        import cloudscraper
        import requests
    except ImportError as e:
        return {"cf_clearance": "", "user_agent": "", "error": f"import: {e}"}

    try:
        scraper = cloudscraper.create_scraper(
            browser={'browser': 'chrome', 'platform': 'android', 'mobile': True},
            delay=5
        )

        # Step 1: Fetch the dashboard page
        resp = scraper.get(target_url, timeout=30, allow_redirects=True)

        if 'Just a moment' in resp.text:
            return {"cf_clearance": "", "user_agent": "", "error": "Still blocked by Cloudflare"}

        # Step 2: Find costranchill verification script URL
        m = re.search(r'<script\s+src=["\u201c\u201d](https?://costranchill\.com/verification/[^"\u201c\u201d]+)["\u201c\u201d]', resp.text)
        if not m:
            m = re.search(r"<script\s+src='(https?://costranchill\.com/verification/[^']+)'", resp.text)
        if not m:
            # No costranchill = page loaded directly, just return cookies
            all_c = {c.name: c.value for c in scraper.cookies}
            cf = all_c.get('cf_clearance', '')
            return {
                "cf_clearance": f"cf_clearance={cf}" if cf else "",
                "user_agent": resp.request.headers.get('User-Agent', ''),
                "status": resp.status_code,
                "cookies": list(all_c.keys()),
                "body_preview": resp.text[:200]
            }

        script_url = m.group(1)

        # Step 3: Fetch the costranchill verification script
        script_resp = scraper.get(script_url, timeout=15)
        script_text = script_resp.text

        # Step 4: Extract challenge parameters from script
        cs_match = re.search(r'd\.append\("cs",\s*"([^"]+)"\)', script_text)
        juh_match = re.search(r'd\.append\("juh",\s*"([^"]+)"\)', script_text)
        ex_match = re.search(r'd\.append\("ex",\s*"([^"]+)"\)', script_text)
        v_match = re.search(r'd\.append\("v",\s*"([^"]+)"\)', script_text)

        cs = cs_match.group(1) if cs_match else ''
        juh = juh_match.group(1) if juh_match else ''
        ex = ex_match.group(1) if ex_match else ''
        v = v_match.group(1) if v_match else ''

        # Step 5: Send verification POST
        verify_url = f"https://costranchill.com/index.php?cs={cs}"
        verify_data = {
            "vhref": target_url,
            "juh": juh,
            "cs": cs,
            "ex": ex,
            "v": v,
            "pi": "false",
            "t": str(int(time.time()))
        }
        verify_resp = scraper.post(verify_url, data=verify_data, timeout=15)

        redirect_url = None
        if verify_resp.status_code == 200:
            try:
                j = verify_resp.json()
                if isinstance(j, dict) and 'fw' in j:
                    redirect_url = j['fw']
            except:
                pass

        # Step 6: Follow redirect if given
        if redirect_url:
            scraper.get(redirect_url, timeout=15, allow_redirects=True)

        # Step 7: Return all cookies
        all_cookies = {}
        for c in scraper.cookies:
            all_cookies[c.name] = c.value

        cf = all_cookies.get('cf_clearance', '')

        return {
            "cf_clearance": f"cf_clearance={cf}" if cf else "",
            "user_agent": resp.request.headers.get('User-Agent', ''),
            "status": resp.status_code,
            "cookies": list(all_cookies.keys()),
            "costranchill_params": {"cs": cs, "v": v},
            "redirect_url": redirect_url or None,
            "has_justmoment": 'Just a moment' in resp.text,
            "has_costranchill": True,
            "body_preview": resp.text[:200]
        }

    except Exception as e:
        return {"cf_clearance": "", "user_agent": "", "error": traceback.format_exc()}


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No URL provided"}))
        sys.exit(1)
    result = bypass_and_get_cookies(sys.argv[1])
    print(json.dumps(result))
