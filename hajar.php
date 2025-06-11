<?php

// ฟังก์ชันเพื่อสร้างไฟล์ Sitemap
function createSitemap($urls, $fileName) {
    $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xmlContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // สร้าง <url> สำหรับแต่ละ URL ที่ดึงมาจาก hajar.txt
    foreach ($urls as $url) {
        $xmlContent .= '  <url>' . "\n";
        $xmlContent .= '    <loc>' . $url . '</loc>' . "\n";
        $xmlContent .= '  </url>' . "\n";
    }

    $xmlContent .= '</urlset>';

    // เขียนเนื้อหา XML ไปยังไฟล์
    file_put_contents($fileName, $xmlContent);
}

// ดึงข้อมูลจาก hajar.txt
$keywords = file('hajar.txt', FILE_IGNORE_NEW_LINES);

// URL พื้นฐาน
$base_url = 'https://bandonchomphu.ac.th/th/?play=';

// กำหนดจำนวน URL ที่จะใส่ในแต่ละไฟล์
$urls_per_file = 10000;

// สร้างไฟล์ Sitemap หลัก (play.xml)
$sitemapIndex = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemapIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// ตัวแปรสำหรับเก็บลิงก์ URL
$allUrls = [];

// สร้าง sitemap ตามจำนวนที่กำหนด
$fileCount = 1;
$currentUrls = [];
$base_sitemap_url = 'https://bandonchomphu.ac.th/th/'; // URL base สำหรับไฟล์ Sitemap

foreach ($keywords as $keyword) {
    $currentUrls[] = $base_url . urlencode($keyword);

    // หากจำนวน URL ถึง 3000 ก็ให้สร้างไฟล์ใหม่
    if (count($currentUrls) >= $urls_per_file) {
        // สร้างไฟล์ Sitemap สำหรับกลุ่ม URL นี้
        $sitemapFile = "play-" . $fileCount . ".xml";
        createSitemap($currentUrls, $sitemapFile);

        // เพิ่ม URL ของไฟล์ Sitemap ไปใน sitemap index พร้อมกับ URL สมบูรณ์
        $sitemapIndex .= '  <sitemap>' . "\n";
        $sitemapIndex .= '    <loc>' . $base_sitemap_url . $sitemapFile . '</loc>' . "\n";
        $sitemapIndex .= '  </sitemap>' . "\n";

        // รีเซ็ตตัวแปรเพื่อสร้างไฟล์ถัดไป
        $fileCount++;
        $currentUrls = [];
    }
}

// หากมี URL ที่เหลืออยู่ในกลุ่มสุดท้าย (น้อยกว่า 3000)
if (count($currentUrls) > 0) {
    $sitemapFile = "play-" . $fileCount . ".xml";
    createSitemap($currentUrls, $sitemapFile);

    // เพิ่ม URL ของไฟล์ Sitemap ไปใน sitemap index
    $sitemapIndex .= '  <sitemap>' . "\n";
    $sitemapIndex .= '    <loc>' . $base_sitemap_url . $sitemapFile . '</loc>' . "\n";
    $sitemapIndex .= '  </sitemap>' . "\n";
}

// ปิดแท็ก sitemapindex
$sitemapIndex .= '</sitemapindex>';

// สร้างไฟล์ sitemap.xml (index ของ sitemap ทั้งหมด)
file_put_contents('play.xml', $sitemapIndex);

echo "Sitemap ได้รับการสร้างเรียบร้อยแล้ว!";
?>
