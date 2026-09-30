<?php

namespace App\Support;

class SpreadsheetSanitizer
{
    /**
     * Escape cell text value to prevent Spreadsheet Formula / CSV Injection (CWE-1236).
     *
     * This escapes strings beginning with formula characters (=, +, -, @), tabs,
     * or line breaks when rendered in spreadsheets, ensuring they are treated as
     * literal text rather than executable formulas or command invocations.
     *
     * IMPORTANT: This must only be applied to user-controlled text fields (names,
     * codes, descriptions, notes, etc.) and NEVER to legitimate numeric quantities,
     * signed amounts (+10, -5), prices, or timestamps.
     */
    public static function escape(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Karakter formula berbahaya di awal sel
        $firstChar = substr($value, 0, 1);
        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r", "\n"], true)) {
            return "'" . $value;
        }

        // Jika string diawali whitespace/newline lalu diikuti karakter formula
        $ltrimmed = ltrim($value, " \t\n\r\0\x0B");
        if ($ltrimmed !== '' && in_array(substr($ltrimmed, 0, 1), ['=', '+', '-', '@'], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
