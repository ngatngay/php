<?php

namespace nightmare;

class uuid
{
    public static function v4()
    {
        $b = random_bytes(16);
        $b[6] = chr(ord($b[6]) & 0x0f | 0x40);
        $b[8] = chr(ord($b[8]) & 0x3f | 0x80);

        $hex = bin2hex($b);

        return sprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            substr($hex, 0, 4),
            substr($hex, 4, 4),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 4),
            substr($hex, 24, 4),
            substr($hex, 28, 4)
        );
    }

    // uuidv7 see https://www.rfc-editor.org/rfc/rfc9562#name-uuid-version-7
    public static function v7()
    {
        // 1) Lấy timestamp mili-giây từ Unix Epoch (UTC)
        $unix_ms = (int) (microtime(true) * 1000);

        // 2) 48-bit timestamp big-endian -> 6 byte
        // N = 32-bit big-endian, n = 16-bit big-endian
        $time_bytes = pack('Nn', $unix_ms >> 16, $unix_ms & 0xFFFF);

        // 3) 10 byte ngẫu nhiên (crypto-safe)
        $rand_bytes = random_bytes(10);

        // 4) Ghép thành 16 byte
        $bytes = $time_bytes . $rand_bytes;

        // 5) Set version 7 (0111) vào high nibble của byte thứ 7 (index 6)
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x70);

        // 6) Set variant RFC 4122 -> 10xxxxxx vào byte thứ 9 (index 8)
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        // 7) Chuyển sang dạng string 8-4-4-4-12
        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20),
        );
    }
}
