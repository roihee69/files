<?php
function getFileRowCount($filename)
{
    $file = fopen($filename, "r");
    $rowCount = 0;

    while (!feof($file)) {
        fgets($file);
        $rowCount++;
    }

    fclose($file);
    return $rowCount;
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$fullUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (isset($fullUrl)) {
    $parsedUrl = parse_url($fullUrl);
    $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : '';
    $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    $baseUrl = $scheme . "://" . $host . $path;
    $urlAsli = str_replace("get-sitemap.php", "", $baseUrl);

    $fileList = ['list.txt', 'list-1.txt', 'list-2.txt'];
    $allLines = [];

    foreach ($fileList as $judulFile) {
        if (file_exists($judulFile)) {
            $lines = file($judulFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $allLines = array_merge($allLines, $lines);
        }
    }

    $sitemapLimit = 10000;
    $sitemapCount = 1;
    $entryCount = 0;
    $sitemapFiles = [];

    $filename = "play-{$sitemapCount}.xml";
    $sitemapFiles[] = $filename;
    $sitemapFile = fopen($filename, "w");

    fwrite($sitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
    fwrite($sitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);

    date_default_timezone_set('Asia/Bangkok');
    $currentTime = date('Y-m-d\TH:i:sP');

    foreach ($allLines as $judul) {
        if ($entryCount >= $sitemapLimit) {
            fwrite($sitemapFile, '</urlset>' . PHP_EOL);
            fclose($sitemapFile);

            $sitemapCount++;
            $entryCount = 0;
            $filename = "play-{$sitemapCount}.xml";
            $sitemapFiles[] = $filename;
            $sitemapFile = fopen($filename, "w");

            fwrite($sitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
            fwrite($sitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);
        }

        $sitemapLink = $urlAsli . '?play=' . urlencode($judul);
        fwrite($sitemapFile, '  <url>' . PHP_EOL);
        fwrite($sitemapFile, '    <loc>' . $sitemapLink . '</loc>' . PHP_EOL);
        fwrite($sitemapFile, '    <lastmod>' . $currentTime . '</lastmod>' . PHP_EOL);
        fwrite($sitemapFile, '    <changefreq>daily</changefreq>' . PHP_EOL);
        fwrite($sitemapFile, '  </url>' . PHP_EOL);

        $entryCount++;
    }

    fwrite($sitemapFile, '</urlset>' . PHP_EOL);
    fclose($sitemapFile);

    $sitemapIndex = fopen("play.xml", "w");
    fwrite($sitemapIndex, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
    fwrite($sitemapIndex, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);

    foreach ($sitemapFiles as $file) {
        fwrite($sitemapIndex, '  <sitemap>' . PHP_EOL);
        fwrite($sitemapIndex, '    <loc>' . $urlAsli . $file . '</loc>' . PHP_EOL);
        fwrite($sitemapIndex, '    <lastmod>' . $currentTime . '</lastmod>' . PHP_EOL);
        fwrite($sitemapIndex, '  </sitemap>' . PHP_EOL);
    }

    fwrite($sitemapIndex, '</sitemapindex>' . PHP_EOL);
    fclose($sitemapIndex);

    $robotsTxt = "User-agent: *" . PHP_EOL;
    $robotsTxt .= "Allow: /" . PHP_EOL;
    $robotsTxt .= "Sitemap: " . $urlAsli . "play.xml" . PHP_EOL;
    file_put_contents('robots.txt', $robotsTxt);

    echo "✅ สร้าง play.xml และ " . count($sitemapFiles) . " ไฟล์ย่อยสำเร็จแล้ว";
} else {
    echo "https://google.com.";
}
?>
