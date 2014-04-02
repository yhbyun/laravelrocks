<?php

class MyString {


    /**
     * Str::slug의 수정함수. 한글에서 동작하게 변경
     *
     * @param $title
     * @param string $separator
     * @return string
     */
    public static function slug($title, $separator = '-') {
		// Convert all dashes/undescores into separator
		$flip = $separator == '-' ? '_' : '-';

		$title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

    /**
     * 문자열을 지정길이 이하로 자르기. 잘리는 곳은 워드 단위다.
     *
     * @param $str
     * @param int $n
     * @param string $end_char
     * @return mixed|string
     */
    public static function cutString($str, $n = 500, $end_char = '&#8230;') {
        if (strlen($str) < $n) {
            return $str;
        }

        $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

        if (strlen($str) <= $n) {
            return $str;
        }

        $out = "";
        foreach (explode(' ', trim($str)) as $val) {
            if (strlen($out . $val . ' ') >= $n) {
                $out = trim($out);
                return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
            }

            $out .= $val.' ';
        }
    }
} 