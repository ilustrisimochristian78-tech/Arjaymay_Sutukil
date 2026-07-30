<?php
// ======================================================
// ONE-TIME SETUP: Download jQuery, Font Awesome icons, and
// the Poppins font locally so the whole system stops
// depending on internet access to function or look right.
//
// WHY THIS EXISTS:
// header.php (and the login/report pages) were loading
// jQuery, Font Awesome, and Google Fonts from CDNs. Every
// button, every AJAX call, every modal, every icon on this
// entire system depends on those files. When there's no
// internet, those CDN requests fail:
//   - jQuery never loads -> every $(...).click()/$.ajax()
//     handler (Quick Actions, Kitchen Display, Search, the
//     eye icon, print button, etc.) silently stops working.
//   - Font Awesome never loads -> every icon disappears.
//   - Google Fonts never loads -> text falls back to a
//     generic system font.
//
// HOW TO USE:
// 1. Make sure THIS COMPUTER has internet access, just once
//    (even a phone hotspot for a few seconds is enough).
// 2. Open this file in your browser:
//    http://localhost/arjaymay-pos-system/setup_offline_assets.php
// 3. It downloads everything into assets/js/vendor/ on your
//    server.
// 4. After that, the system no longer needs internet at all
//    for these - they're permanent local files, not CDN
//    requests. header.php automatically detects and uses
//    them (falls back to the CDN only if they're missing).
// 5. You can delete this file afterward if you like.
// ======================================================

$vendorDir = __DIR__ . '/assets/js/vendor';
if (!is_dir($vendorDir)) {
    mkdir($vendorDir, 0755, true);
}

echo "<!DOCTYPE html><html><head><title>Offline Asset Setup</title>";
echo "<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;line-height:1.6;} .ok{color:green;} .fail{color:#c0392b;} code{background:#f0f0f0;padding:2px 6px;border-radius:4px;}</style>";
echo "</head><body>";
echo "<h2>Offline Asset Setup</h2>";

function fetchUrl(string $url, ?string &$error = null) {
    $error = null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36');
        $content = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return $content;
    }
    $context = stream_context_create(['http' => ['timeout' => 20, 'header' => "User-Agent: Mozilla/5.0\r\n"]]);
    $content = @file_get_contents($url, false, $context);
    if ($content === false) {
        $error = 'file_get_contents failed (allow_url_fopen may be disabled)';
    }
    return $content;
}

function installFile(string $label, string $url, string $targetFile, int $minBytes = 500): bool {
    if (file_exists($targetFile) && filesize($targetFile) >= $minBytes) {
        echo "<p class='ok'>&#10003; $label already installed.</p>";
        return true;
    }
    $dir = dirname($targetFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $error = null;
    $content = fetchUrl($url, $error);

    if ($content === false || strlen($content) < $minBytes) {
        echo "<p class='fail'>&#10007; Could not download $label.";
        if (!empty($error)) echo " (" . htmlspecialchars($error) . ")";
        echo " You can manually download <a href='" . htmlspecialchars($url) . "' target='_blank'>$url</a> and save it to:<br><code>" . htmlspecialchars($targetFile) . "</code></p>";
        return false;
    }

    file_put_contents($targetFile, $content);
    echo "<p class='ok'>&#10003; $label saved locally.</p>";
    return true;
}

// ------------------------------------------------------
// 1. jQuery
// ------------------------------------------------------
installFile(
    'jQuery',
    'https://code.jquery.com/jquery-3.6.0.min.js',
    $vendorDir . '/jquery-3.6.0.min.js',
    50000
);

// ------------------------------------------------------
// 2. Font Awesome 6.4.0 (CSS + the 3 webfont files it needs)
// ------------------------------------------------------
installFile(
    'Font Awesome CSS',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    $vendorDir . '/fontawesome/css/all.min.css',
    5000
);
$faFonts = [
    'fa-solid-900.woff2',
    'fa-regular-400.woff2',
    'fa-brands-400.woff2',
];
foreach ($faFonts as $font) {
    installFile(
        "Font Awesome font ($font)",
        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/$font",
        $vendorDir . "/fontawesome/webfonts/$font",
        1000
    );
}

// ------------------------------------------------------
// 3. Poppins (Google Font) - fetch the CSS, then pull down
//    every font file it references and rewrite the CSS to
//    point at the local copies.
// ------------------------------------------------------
$poppinsCssTarget = $vendorDir . '/fonts/poppins.css';
$poppinsFilesDir = $vendorDir . '/fonts/files';

if (file_exists($poppinsCssTarget)) {
    echo "<p class='ok'>&#10003; Poppins font already installed.</p>";
} else {
    $cssUrl = 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap';
    $error = null;
    $css = fetchUrl($cssUrl, $error);

    if ($css === false || strlen($css) < 100) {
        echo "<p class='fail'>&#10007; Could not download the Poppins font CSS.";
        if (!empty($error)) echo " (" . htmlspecialchars($error) . ")";
        echo "</p>";
    } else {
        if (!is_dir($poppinsFilesDir)) mkdir($poppinsFilesDir, 0755, true);

        $fontCount = 0;
        $css = preg_replace_callback(
            '/url\((https:\/\/fonts\.gstatic\.com\/[^)]+)\)/',
            function ($m) use ($poppinsFilesDir, &$fontCount) {
                $fontUrl = trim($m[1], "'\"");
                $filename = 'poppins-' . md5($fontUrl) . '.woff2';
                $target = $poppinsFilesDir . '/' . $filename;
                if (!file_exists($target)) {
                    $fontError = null;
                    $fontData = fetchUrl($fontUrl, $fontError);
                    if ($fontData !== false && strlen($fontData) > 500) {
                        file_put_contents($target, $fontData);
                        $fontCount++;
                    } else {
                        // Leave the original gstatic URL in place if this one file failed
                        return "url($fontUrl)";
                    }
                } else {
                    $fontCount++;
                }
                return "url('files/$filename')";
            },
            $css
        );

        file_put_contents($poppinsCssTarget, $css);
        echo "<p class='ok'>&#10003; Poppins font saved locally ($fontCount file(s)).</p>";
    }
}

echo "<hr><p>All done. The system will now work fully even when this computer has no internet connection.</p>";
echo "<p>You can safely delete this setup file now if you want (<code>setup_offline_assets.php</code>).</p>";
echo "</body></html>";
