<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_external_ip')) {
    function get_external_ip()
    {
        // Batasi waktu mencoba
        $options = stream_context_create(array(
            'http' =>
            array(
                'timeout' => 2 //2 seconds
            )
        ));
        $externalContent = file_get_contents('http://checkip.dyndns.com/', false, $options);
        preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $externalContent, $m);
        $externalIp = $m[0];
        return $externalIp;
    }
}

if (!function_exists('d')) {
    function d($x)
    {
        return die(json_encode($x));
    }
}

if (!function_exists('asset')) {
    function asset($path = NULL)
    {
        return base_url("assets/$path");
    }
}

//FUNCTION INI BELUM BERJALAN DENGAN BAIK
if (!function_exists('e')) {
    function e($data)
    {
        return isset($data) ? $data : "";
    }
}

if (!function_exists('generator')) {
    function generator($length = 7)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
}

if (!function_exists('replace_lower')) {
    function replace_lower($string = "")
    {
        preg_replace("/[^A-Za-z0-9]/", "_", strtolower($string));
    }
}

if (!function_exists('ce')) {
    function ce($string = "")
    {
        return ucwords(strtolower($string));
    }
}

if (!function_exists('sc')) {
    function sc($string = "")
    {
        return ucfirst(strtolower($string));
    }
}

if (!function_exists("set")) {
    function set(&$string)
    {
        return isset($string) ? $string : FALSE;
    }
}



if (!function_exists('tanggal_tampil')) {
    function tanggal_tampil($tanggal = "")
    {
        $originalDate = $tanggal;
        $newDate = date("m/d/Y", strtotime($originalDate));
        return $newDate;
    }
}

if (!function_exists('insert_tanggal')) {
    function insert_tanggal($tanggal = "")
    {
        $newDate = date("Y-m-d", strtotime($tanggal));
        return $newDate;
    }
}

if (!function_exists('alphanumspace')) {
    function alphanumspace($string = "")
    {
        return preg_replace("/[^a-zA-Z0-9 ]+/", "", remove_duplicate_space($string));
    }
}

if (!function_exists("remove_duplicate_space")) {
    function remove_duplicate_space($string = "")
    {
        return preg_replace('/\s+/', ' ', $string);
    }
}

if (!function_exists("dash")) {
    function dash($string = "")
    {
        return str_replace(" ", "-", $string);
    }
}

if(!function_exists("slug")){
    function slug($string = ""){
        return strtolower(dash(remove_duplicate_space(alphanumspace($string))));
    }
}

