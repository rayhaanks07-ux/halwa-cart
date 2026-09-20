<?php
/**
 * Zaika Shahi Halwa - Data Layer Helper
 * Pure PHP Zero-Configuration Data Access with Atomic File Locking
 */

class HalwaDB {
    private static function getFilePath($filename) {
        return __DIR__ . '/../data/' . $filename;
    }

    public static function readJson($filename, $default = []) {
        $path = self::getFilePath($filename);
        if (!file_exists($path)) {
            return $default;
        }
        
        $fp = fopen($path, 'r');
        if (!$fp) {
            return $default;
        }

        flock($fp, LOCK_SH);
        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 8192);
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        $data = json_decode($content, true);
        return is_array($data) ? $data : $default;
    }

    public static function writeJson($filename, $data) {
        $path = self::getFilePath($filename);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        $fp = fopen($path, 'c+');
        if (!$fp) {
            return false;
        }

        flock($fp, LOCK_EX);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }

    public static function generateOrderId() {
        return 'ZSH-' . rand(1000, 9999);
    }
}
