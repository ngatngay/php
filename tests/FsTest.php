<?php

declare(strict_types=1);

use nightmare\fs;
use PHPUnit\Framework\TestCase;

final class FsTest extends TestCase
{
    public function testGetOwnerNameMatchesPosixLookupForExistingFile(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'fs_test_');
        self::assertIsString($tempFile);

        try {
            $ownerId = fileowner($tempFile);
            self::assertIsInt($ownerId);

            $expected = posix_getpwuid($ownerId)['name'] ?? '';
            self::assertSame($expected, fs::get_owner_name($tempFile));
        } finally {
            @unlink($tempFile);
        }
    }

    public function testGetOwnerNameReturnsEmptyStringForMissingFile(): void
    {
        $missingPath = sys_get_temp_dir() . '/fs_test_missing_' . uniqid('', true);
        self::assertFileDoesNotExist($missingPath);

        self::assertSame('', fs::get_owner_name($missingPath));
    }

    public function testGetOwnerNameByIdReturnsNameForExistingUser(): void
    {
        $currentUserId = posix_geteuid();
        $expected = posix_getpwuid($currentUserId)['name'] ?? '';

        self::assertSame($expected, fs::get_owner_name_by_id($currentUserId));
    }

    public function testGetOwnerNameByIdReturnsEmptyStringForUnknownUser(): void
    {
        self::assertSame('', fs::get_owner_name_by_id(PHP_INT_MAX));
    }
}

