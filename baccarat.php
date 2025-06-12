<?php

function createSitemap($urls, $fileName) {
    $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xmlContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $url) {
        $xmlContent .= '  <url>' . "\n";
        $xmlContent .= '    <loc>' . $url . '</loc>' . "\n";
        $xmlContent .= '  </url>' . "\n";
    }

    $xmlContent .= '</urlset>';

    file_put_contents($fileName, $xmlContent);
}

$keywords = file('baccarat.txt', FILE_IGNORE_NEW_LINES);

$base_url = 'http://app.csit.sci.tsu.ac.th/admins/lazada.php?play=';

$urls_per_file = 5000;

$sitemapIndex = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemapIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

$allUrls = [];

$fileCount = 1;
$currentUrls = [];
$base_sitemap_url = 'http://app.csit.sci.tsu.ac.th/admins/'; // URL base สำหรับไฟล์ Sitemap

foreach ($keywords as $keyword) {
    $currentUrls[] = $base_url . urlencode($keyword);

    if (count($currentUrls) >= $urls_per_file) {
        $sitemapFile = "sitemap-" . $fileCount . ".xml";
        createSitemap($currentUrls, $sitemapFile);

        $sitemapIndex .= '  <sitemap>' . "\n";
        $sitemapIndex .= '    <loc>' . $base_sitemap_url . $sitemapFile . '</loc>' . "\n";
        $sitemapIndex .= '  </sitemap>' . "\n";

        $fileCount++;
        $currentUrls = [];
    }
}

if (count($currentUrls) > 0) {
    $sitemapFile = "sitemap-" . $fileCount . ".xml";
    createSitemap($currentUrls, $sitemapFile);

    $sitemapIndex .= '  <sitemap>' . "\n";
    $sitemapIndex .= '    <loc>' . $base_sitemap_url . $sitemapFile . '</loc>' . "\n";
    $sitemapIndex .= '  </sitemap>' . "\n";
}

$sitemapIndex .= '</sitemapindex>';

file_put_contents('sitemap.xml', $sitemapIndex);

echo "Sitemap ได้รับการสร้างเรียบร้อยแล้ว!";
?>
