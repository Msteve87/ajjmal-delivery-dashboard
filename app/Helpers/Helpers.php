<?php

if (!function_exists('sanitize_html_string')) {
    function sanitize_html_string($string)
    {
        $result = str_replace('&nbsp;', '', $string);
        $result = strip_tags($string);
        return htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
    }
}
