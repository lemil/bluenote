<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Strings Helper
 *
 */
function sanitize($v, $filter, $options = array(), $maxlen = 0) {
    $a = $v;
    //Type-cast filter
    $a = filter_var($a, $filter, $options);

    if ($filter == FILTER_SANITIZE_EMAIL) {
        //Nada
    } else {
        $a = preg_replace("/[^a-zA-Z0-9@]+/", "", $a);
    }

    //Trim to maxlen
    if ($maxlen == 0) {
        //Nada
    } else {
        $a = substr($a, 0, $maxlen);
    }

    //XSS and SQLi
    $a = sanitize_xss_sqli($a);

    return $a;
}


function sanitize_xss_sqli($v) {
    $a = $v;

    //XSS Filter
    $a = strip_tags($a);

    //SQLi Filter , similar to mysql_real_escape_string()
    $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a", "--");
    $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z", "");
    $a = str_replace($search, $replace, $a);

    //If somthing strange found, retuns an empty string FYU.
    if ($v != $a) {
        $a = '';
    }
    return $a;
}

function header_status($statusCode) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    $j = array('r' => 0, 't' => time(), 'd' => 100070);
    echo strtolower(json_encode($j));
    die();
}

function format_price($unformatted_price) {

    return $unformatted_price < 1000 ?
            '$' . strtoupper(number_format($unformatted_price, 2, ',', '.')) :
            '$' . strtoupper(number_format($unformatted_price, 0, ',', '.'));
}

function format_sell_unit($sell_unit) {
    $formatted = '';
    if ($sell_unit != null && strlen($sell_unit) > 1) {
        $formatted = substr($sell_unit, 0, 1) . substr($sell_unit, 1, strlen($sell_unit) - 1);
    }
    return 'x' . $formatted . ' con IVA';
}


function validate_field($val,$flag) {
    $r = true;
    if($r && $flag == 'required') {
        $r = (isset($val) && strlen(''.$val) > 0);
    }
    return $r;
}



?>
