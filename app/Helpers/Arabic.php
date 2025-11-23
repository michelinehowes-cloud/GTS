<?php

namespace App\Helpers;

class Arabic
{
    public static function reshape($str)
    {
        if (!$str)
            return '';

        // Define Presentation Forms B (Hex codes)
        // 0: Isolated, 1: Final, 2: Initial, 3: Medial
        $presentation_forms = [
            'ء' => ['fe80', '', '', ''],
            'آ' => ['fe81', 'fe82', '', ''],
            'أ' => ['fe83', 'fe84', '', ''],
            'ؤ' => ['fe85', 'fe86', '', ''],
            'إ' => ['fe87', 'fe88', '', ''],
            'ئ' => ['fe89', 'fe8a', 'fe8b', 'fe8c'],
            'ا' => ['fe8d', 'fe8e', '', ''],
            'ب' => ['fe8f', 'fe90', 'fe91', 'fe92'],
            'ة' => ['fe93', 'fe94', '', ''],
            'ت' => ['fe95', 'fe96', 'fe97', 'fe98'],
            'ث' => ['fe99', 'fe9a', 'fe9b', 'fe9c'],
            'ج' => ['fe9d', 'fe9e', 'fe9f', 'fea0'],
            'ح' => ['fea1', 'fea2', 'fea3', 'fea4'],
            'خ' => ['fea5', 'fea6', 'fea7', 'fea8'],
            'د' => ['fea9', 'feaa', '', ''],
            'ذ' => ['feab', 'feac', '', ''],
            'ر' => ['fead', 'feae', '', ''],
            'ز' => ['feaf', 'feb0', '', ''],
            'س' => ['feb1', 'feb2', 'feb3', 'feb4'],
            'ش' => ['feb5', 'feb6', 'feb7', 'feb8'],
            'ص' => ['feb9', 'feba', 'febb', 'febc'],
            'ض' => ['febd', 'febe', 'febf', 'fec0'],
            'ط' => ['fec1', 'fec2', 'fec3', 'fec4'],
            'ظ' => ['fec5', 'fec6', 'fec7', 'fec8'],
            'ع' => ['fec9', 'feca', 'fecb', 'fecc'],
            'غ' => ['fecd', 'fece', 'fecf', 'fed0'],
            'ف' => ['fed1', 'fed2', 'fed3', 'fed4'],
            'ق' => ['fed5', 'fed6', 'fed7', 'fed8'],
            'ك' => ['fed9', 'feda', 'fedb', 'fedc'],
            'ل' => ['fedd', 'fede', 'fedf', 'fee0'],
            'م' => ['fee1', 'fee2', 'fee3', 'fee4'],
            'ن' => ['fee5', 'fee6', 'fee7', 'fee8'],
            'ه' => ['fee9', 'feea', 'feeb', 'feec'],
            'و' => ['feed', 'feee', '', ''],
            'ى' => ['feef', 'fef0', '', ''],
            'ي' => ['fef1', 'fef2', 'fef3', 'fef4'],
            'لا' => ['fefb', 'fefc', '', ''],
        ];

        // Characters that allow connection to the LEFT (next character)
        $connecting_chars = [
            'ئ',
            'ب',
            'ت',
            'ث',
            'ج',
            'ح',
            'خ',
            'س',
            'ش',
            'ص',
            'ض',
            'ط',
            'ظ',
            'ع',
            'غ',
            'ف',
            'ق',
            'ك',
            'ل',
            'م',
            'ن',
            'ه',
            'ي'
        ];

        // Split string into array of characters
        preg_match_all('/./us', $str, $matches);
        $chars = $matches[0];
        $len = count($chars);
        $output = '';

        for ($i = 0; $i < $len; $i++) {
            $current = $chars[$i];

            // If character is not in our map, just append it
            if (!isset($presentation_forms[$current])) {
                $output .= $current;
                continue;
            }

            // Check Previous Connection
            $prev = ($i > 0) ? $chars[$i - 1] : null;
            $connect_prev = false;
            if ($prev && in_array($prev, $connecting_chars)) {
                $connect_prev = true;
            }

            // Check Next Connection
            $next = ($i < $len - 1) ? $chars[$i + 1] : null;
            $connect_next = false;
            if ($next && isset($presentation_forms[$next])) {
                // Next char is Arabic, so it CAN connect from right.
                // But does CURRENT char allow connection to left?
                if (in_array($current, $connecting_chars)) {
                    $connect_next = true;
                }
            }

            // Determine Form
            // 0: Isolated, 1: Final, 2: Initial, 3: Medial
            $form = 0;
            if ($connect_prev && $connect_next) {
                $form = 3; // Medial
            } elseif ($connect_prev) {
                $form = 1; // Final
            } elseif ($connect_next) {
                $form = 2; // Initial
            } else {
                $form = 0; // Isolated
            }

            // Fallback for chars that don't have all forms (like Aleph)
            if (empty($presentation_forms[$current][$form])) {
                if ($form == 2)
                    $form = 0; // Initial -> Isolated
                if ($form == 3)
                    $form = 1; // Medial -> Final
            }

            $hex = $presentation_forms[$current][$form];

            if ($hex) {
                // Use json_decode for reliable unicode character generation
                $output .= json_decode('"\u' . $hex . '"');
            } else {
                $output .= $current;
            }
        }

        return $output;
    }
}
