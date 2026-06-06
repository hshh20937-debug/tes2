<?php

error_reporting(0);
date_default_timezone_set('Asia/Jakarta');
$configFile = "config.json";

const hitam  = "\033[0;30m";
const merah  = "\033[0;31m";
const hijau  = "\033[0;32m";
const kuning = "\033[0;33m";
const biru   = "\033[0;34m";
const cyan   = "\033[0;36m";
const putih  = "\033[0;37m";
const reset  = "\033[0m";

const version     = "1.0";
const script_name = "99faucet.com";
const host        = "https://99faucet.com";
const api_in      = "https://api.waryono.my.id/in.php";

function uf() {
    return md5(uniqid(mt_rand(), true));
}

function skibidixxx($url, $method = 'GET', $data = [], $headers = []) {
    while (true) {
        $ch = curl_init();
        $final_headers = [];
        foreach ($headers as $header) {
            $final_headers[] = $header;
        }
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => $final_headers,
            CURLOPT_CONNECTTIMEOUT => 999,
            CURLOPT_TIMEOUT        => 999
        ];
        if (strtoupper($method) === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if ($response) {
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);
            curl_close($ch);
            return $body;
        } else {
            curl_close($ch);
            echo "\33[1;" . rand(30, 37) . "mwiwok detok";
            sleep(1);
            echo "\r \r";
            return "ngelek";
        }
    }
}

function timer($seconds, $prefix = "[!] please wait") {
    $wait_time = (int)$seconds;
    $frames = ['⣾', '⣽', '⣻', '⢿', '⡿', '⣟', '⣯', '⣷'];
    $frame_count = count($frames);
    $current_frame = 0;
    $frame_delay = 0.1;
    while ($wait_time > 0) {
        $start_time = microtime(true);
        while ((microtime(true) - $start_time) < 1) {
            $hours = floor($wait_time / 3600);
            $minutes = floor(($wait_time % 3600) / 60);
            $seconds_left = $wait_time % 60;
            $time_formatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds_left);
            $spinner = $frames[$current_frame];
            echo putih . $prefix . hijau . " $time_formatted " . putih . $spinner . "\r";
            usleep($frame_delay * 1000000);
            $current_frame = ($current_frame + 1) % $frame_count;
            if ((microtime(true) - $start_time) >= 1) break;
        }
        $wait_time--;
    }
    echo "\r                                     \r";
}

function slider($app_id, $public_key, $version, $reff, $apikey) {
    $headers = ["Content-Type: application/json"];
    $body = json_encode([
        "apikey"     => $apikey,
        "app_id"     => $app_id,
        "methods"    => "rslider",
        "public_key" => $public_key,
        "version"    => $version,
        "referer"    => $reff,
        "json"       => 1
    ]);
    $request = skibidixxx(api_in, "POST", $body, $headers);
    if (strpos($request, "ERROR_WRONG_METHOD") !== false) { echo putih."Error: ".merah."ERROR_WRONG_METHOD\n"; exit; }
    if (strpos($request, "ERROR_KEY_DOES_NOT_EXIST") !== false) { echo putih."Error: ".merah."ERROR_KEY_DOES_NOT_EXIST\n"; exit; }
    if (strpos($request, "ERROR_METHOD_NOT_SPECIFIED") !== false) { echo putih."Error: ".merah."ERROR_METHOD_NOT_SPECIFIED\n"; exit; }
    if (strpos($request, "ERROR_NO_SUCH_METHOD") !== false) { echo putih."Error: ".merah."ERROR_NO_SUCH_METHOD\n"; exit; }
    if (strpos($request, "ERROR_DATABASE_CONNECTION_FAILED") !== false) { echo putih."Error: ".merah."ERROR_DATABASE_CONNECTION_FAILED\n"; exit; }
    if (strpos($request, "ERROR_TOO_MANY_REQUESTS") !== false) { echo putih."Error: ".merah."ERROR_TOO_MANY_REQUESTS"; sleep(1.8); echo "\r                                               \r"; return "ERROR_TOO_MANY_REQUESTS"; }
    if (strpos($request, "ERROR_WRONG_USER_KEY") !== false) { echo putih."Error: ".merah."ERROR_WRONG_USER_KEY\n"; exit; }
    if (strpos($request, "ERROR_ZERO_BALANCE") !== false) { echo putih."Error: ".merah."ERROR_ZERO_BALANCE\n"; exit; }
    if (strpos($request, "ERROR_BAD_PARAMETERS") !== false) { echo putih."Error: ".merah."ERROR_BAD_PARAMETERS\n"; exit; }
    if (strpos($request, "ERROR_EMPTY_IMAGE") !== false) { echo putih."Error: ".merah."ERROR_EMPTY_IMAGE\n"; exit; }
    if (strpos($request, "ERROR_UNKNOWN") !== false) { echo putih."Error: ".merah."ERROR_UNKNOWN\n"; exit; }
    $json = json_decode($request, true);
    $id = $json["request"];
    reload:
    timer(3);
    $url = "https://api.waryono.my.id/res.php?apikey=".$apikey."&id=".$id."&json=1";
    $result = skibidixxx($url, "GET", []);
    if (strpos($result, "ERROR_BAD_PARAMETERS") !== false) { echo putih."Error: ".merah."ERROR_BAD_PARAMETERS\n"; exit; }
    if (strpos($result, "Database connection failed") !== false) { echo putih."Error: ".merah."Database connection failed\n"; exit; }
    if (strpos($result, "WRONG_CAPTCHA_ID") !== false) { echo putih."Error: ".merah."WRONG_CAPTCHA_ID"; sleep(1.8); echo "\r                                               \r"; return "WRONG_CAPTCHA_ID"; }
    if (strpos($result, "ERROR_SOLVE_PENDING") !== false) { echo putih."Error: ".merah."ERROR_SOLVE_PENDING"; sleep(1.8); echo "\r                                               \r"; return "ERROR_SOLVE_PENDING"; }
    if (strpos($result, "CAPCHA_NOT_READY") !== false) { echo putih."Error: ".merah."CAPCHA_NOT_READY"; sleep(1.8); echo "\r                                               \r"; goto reload; }
    if (strpos($result, "ERROR_CAPTCHA_UNSOLVABLE") !== false) { echo putih."Error: ".merah."ERROR_CAPTCHA_UNSOLVABLE"; sleep(1.8); echo "\r                                               \r"; return "ERROR_CAPTCHA_UNSOLVABLE"; }
    if (strpos($result, "ERROR_BAD_REQUEST") !== false) { echo "Error: ".merah."ERROR_BAD_REQUEST\n"; exit; }
    if (strpos($result, "INTENAL_SERVER_ERROR") !== false) { echo "Errro: ".merah."INTENAL_SERVER_ERROR"; sleep(1.8); echo "\r                                               \r"; return "INTENAL_SERVER_ERROR"; }
    $json = json_decode($result, true);
    $res = $json["request"];
    preg_match('/rs_token:(\d+),rs_res:([^,]+)/', $res, $match);
    return [
        "rs_token" => $match[1], 
        "rs_res"   => $match[2]
    ];
}

function bypassCloudflare(&$config, $configFile, $target) {
    echo putih . "Cloudflare! wait..\n";
    $python_bin = trim(shell_exec("which python3 2>/dev/null") ?: shell_exec("which python 2>/dev/null") ?: "/usr/local/bin/python");
    $python_bin = trim($python_bin);
    echo putih . "Python: " . $python_bin . "\n";
    $test_out = trim(shell_exec($python_bin . " -c \"print('PYOK')\" 2>&1") ?? '');
    if ($test_out !== 'PYOK') {
        echo merah . "Python test failed: " . $test_out . "\n";
        echo putih."------------------------------------------------------\n";
        return false;
    }
    echo hijau . "Python OK\n";
    $python_cmd = $python_bin . " exec.py " . escapeshellarg($target) . " 2>&1";
    $output = trim(shell_exec($python_cmd) ?? '');
    $data_bypass = json_decode($output, true);
    if (!$data_bypass) {
        echo merah . "Invalid JSON (" . strlen($output) . " bytes): " . substr($output, 0, 500) . "\n";
        echo putih."------------------------------------------------------\n";
        return false;
    }
    if (isset($data_bypass['error']) && !empty($data_bypass['error'])) {
        echo merah . "Python error: " . $data_bypass['error'] . "\n";
        echo putih."------------------------------------------------------\n";
        return false;
    }
    if (isset($data_bypass['cf_clearance']) && !empty($data_bypass['cf_clearance'])) {
        $full_new_cf = $data_bypass['cf_clearance'];
        $new_ua = $data_bypass['user_agent'];
        $old_cookie = $config['cookie'];
        if (strpos($full_new_cf, '=') !== false) {
            $new_token_value = explode('=', $full_new_cf)[1];
        } else {
            $new_token_value = $full_new_cf;
        }
        $pattern = '/cf_clearance=[^;]+/';
        $replacement = "cf_clearance=" . $new_token_value;
        if (preg_match($pattern, $old_cookie)) {
            $new_cookie_str = preg_replace($pattern, $replacement, $old_cookie);
        } else {
            $new_cookie_str = rtrim($old_cookie, "; ") . "; " . $replacement;
        }
        $config['cookie'] = $new_cookie_str;
        $config['user_agent'] = $new_ua;
        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
        echo hijau . "Success Solver Cloudflare! WAF\n";
        echo putih."------------------------------------------------------\n";
        sleep(2);
        return true;
    } else {
        echo merah . "Error Bypass\n";
        if (isset($data_bypass['status'])) echo putih . "HTTP " . $data_bypass['status'] . "\n";
        if (isset($data_bypass['cookies'])) echo putih . "Cookies: " . implode(", ", $data_bypass['cookies']) . "\n";
        if (!empty($data_bypass['set_cookie_headers'])) echo putih . "Set-Cookie: " . implode(" | ", $data_bypass['set_cookie_headers']) . "\n";
        if (!empty($data_bypass['body_preview'])) echo putih . "Body: " . substr($data_bypass['body_preview'], 0, 300) . "\n";
        if (!empty($data_bypass['has_justmoment'])) echo merah . "Still seeing Just a moment\n";
        if (!empty($data_bypass['has_costranchill'])) echo merah . "costranchill verification detected\n";
        echo putih."------------------------------------------------------\n";
        return false;
    }
}

function getConfig($configFile) {
    if (!file_exists($configFile)) {
        $apikey = getenv('APIKEY');
        $coki   = getenv('COOKIE');
        if (!$apikey || !$coki) {
            echo merah . "Set environment APIKEY and COOKIE first!\n";
            exit(1);
        }
        $data = ["apikey" => $apikey, "cookie" => $coki];
        file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
        echo hijau . "Config created from env vars\n\n" . reset;
        sleep(2);
        return $data;
    }
    return json_decode(file_get_contents($configFile), true);
}

function pickCurrency($currencies, $lastUsed = null) {
    $priority = ['usdt', 'trx', 'ltc', 'doge', 'btc', 'eth', 'bch', 'dash', 'xrp'];
    if ($lastUsed) {
        $currencies = array_values(array_filter($currencies, fn($c) => $c !== $lastUsed));
    }
    foreach ($priority as $p) {
        foreach ($currencies as $c) {
            if (strtolower($c) === $p) return $c;
        }
    }
    return $currencies[array_rand($currencies)];
}

$python_bin = trim(shell_exec("which python3 2>/dev/null") ?: shell_exec("which python 2>/dev/null") ?: "/usr/local/bin/python");
$python_bin = trim($python_bin);
$py_test = trim(shell_exec($python_bin . " -c \"import cloudscraper; print('OK')\" 2>&1") ?? '');
echo putih . "cloudscraper: " . ($py_test === 'OK' ? hijau . "OK" : merah . $py_test) . "\n";
echo putih . "-----------------------------------------------------\n";
sleep(2);

login:
$config = getConfig($configFile);
$apikey = $config['apikey'];
$coki   = $config['cookie'];
$ua     = $config['user_agent'] ?? getenv('USER_AGENT') ?? "Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36";

dash:
$headers = [
    "host: 99faucet.com",
    "user-agent: " . $ua,
    "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,q=0.8,application/signed-exchange;v=b3;q=0.7",
    "referer: ".host."/faucet/pepe",
    "cookie: " . $coki
];

$dash = skibidixxx(host."/dashboard", "GET", [], $headers);

if ($dash == "ngelek" || strpos($dash, "Just a moment") !== false || strpos($dash, "Dashboard | 99Faucet") === false) {
    bypassCloudflare($config, $configFile, host."/dashboard");
    $coki = $config['cookie'];
    $ua   = $config['user_agent'];
    goto dash;
}

if (strpos($dash, "Dashboard | 99Faucet") !== false) {
    preg_match_all('/<a href="https:\/\/99faucet\.com\/faucet\/([^"]+)" class="">/', $dash, $matches);
    $currencies = $matches[1];
    usort($currencies, function($a, $b) {
        return strlen($a) - strlen($b);
    });

    $lastUsed = null;
    $attempts = 0;
    $maxAttempts = count($currencies);

    while ($attempts < $maxAttempts) {
        $memek = pickCurrency($currencies, $lastUsed);
        echo putih . "Trying: " . hijau . strtoupper($memek) . "\n";

        reload:
        while(true){
            $a = [
                "host: 99faucet.com",
                "user-agent: " . $ua,
                "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,q=0.8,application/signed-exchange;v=b3;q=0.7",
                "referer: ".host."/dashboard",
                "cookie: " . $coki
            ];

            $c = [
                "host: 99faucet.com",
                "origin: ".host,
                "content-type: application/x-www-form-urlencoded",
                "user-agent: " . $ua,
                "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,q=0.8,application/signed-exchange;v=b3;q=0.7",
                "referer: https://99faucet.com/faucet/".$memek,
                "cookie: " . $coki
            ];

            $url = host."/faucet/".$memek;
            $faucet = skibidixxx($url, "GET", [], $a);

            if ($faucet == "ngelek" || strpos($faucet, "Just a moment") !== false) {
                bypassCloudflare($config, $configFile, $url);
                $coki = $config['cookie'];
                $ua   = $config['user_agent'];
                goto reload;
            }

            if (strpos($faucet, "Shortlinks | 99Faucet") !== false) {
                echo kuning . "Shortlinks required for " . strtoupper($memek) . ", trying next...\n";
                $lastUsed = $memek;
                $attempts++;
                goto nextCoin;
            }

            preg_match('/<input type="hidden" name="token" value="([^"]+)"/', $faucet, $tokenMatch);
            $token = $tokenMatch[1] ?? '';

            $app_id = "1044";
            $public_key = "ws1WNm5E0xjtnezLT8r9";
            $version = "v5";
            $reff = "https://99faucet.com/";

            $bypass = slider($app_id, $public_key, $version, $reff, $apikey);
            if (is_array($bypass)) {
                $data = http_build_query([
                    "ci_csrf_token" => "",
                    "token" => $token,
                    "currency" => $memek,
                    "captcha" => "rscaptchav37",
                    "rscaptcha_token" => $bypass["rs_token"],
                    "rscaptcha_response" => $bypass["rs_res"],
                    "uf" => uf(),
                    "utt" => "Asia/Jakarta",
                    "ls" => "id,en-US,en,ms,ru"
                ]);
                timer(5);
                $claim = skibidixxx(host."/faucet/verify", "POST", $data, $c);

                if (strpos($claim, "Good job!") !== false) {
                    $msg = explode("'", explode("text: '", $claim)[1])[0];
                    $claimtimer = explode(' -', explode('let wait = ', $claim)[1])[0];
                    echo hijau.$msg."\n";
                    $lastUsed = null;
                    timer($claimtimer);
                } elseif (strpos($claim, "Invalid") !== false) {
                    echo "Invalid captcha or invalid claim!\n";
                    goto reload;
                } elseif (strpos($claim, "The faucet does not have sufficient funds") !== false) {
                    echo kuning . strtoupper($memek) . " insufficient funds, trying next...\n";
                    $lastUsed = $memek;
                    $attempts++;
                    goto nextCoin;
                } else {
                    echo merah . "Unknown error, retrying...\n";
                    sleep(1);
                    goto reload;
                }
            } elseif (in_array($bypass, ["WRONG_CAPTCHA_ID", "ERROR_CAPTCHA_UNSOLVABLE", "ERROR_TOO_MANY_REQUESTS", "ERROR_SOLVE_PENDING", "INTENAL_SERVER_ERROR"])) {
                goto reload;
            } else {
                echo merah . "Unknown captcha error, retrying...\n";
                goto reload;
            }
        }

        nextCoin:
        continue;
    }

    echo merah . "All coins exhausted. Restarting from dashboard...\n";
    sleep(5);
    goto dash;

} else {
    echo putih."Session expired, re-login needed...\n";
    @unlink($configFile);
    sleep(4);
    goto login;
}
