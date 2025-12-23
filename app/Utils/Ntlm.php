<?php

namespace App\Utils;

class Ntlm
{
    /**
     * Compute NTLM v1 NT hash (MD4 of UTF-16LE password)
     */
    public static function ntHash(string $password): string
    {
        // Convert to UTF-16LE (little endian, no BOM)
        $utf16le = iconv('UTF-8', 'UTF-16LE', $password);
        return hash('md4', $utf16le);
    }
}
