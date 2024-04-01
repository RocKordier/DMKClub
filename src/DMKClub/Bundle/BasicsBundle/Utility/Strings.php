<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Utility;

class Strings
{
    /**
     * Cleanup filename
     * http://stackoverflow.com/questions/2021624/string-sanitizer-for-filename.
     */
    public static function sanitizeFilename(string $filename): false|string|null
    {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;[]().
        // If you don't need to handle multi-byte characters
        // you can use preg_replace rather than mb_ereg_replace
        // Thanks @Łukasz Rysiak!
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        // Remove any runs of periods (thanks falstro!)
        return mb_ereg_replace("([\.]{2,})", '', $filename);
    }
}
