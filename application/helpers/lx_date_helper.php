<?php
function mysql_gm_spain($date)
{
    $ci = &get_instance();
    $ci->load->helper('date');
    return unix_to_human(gmt_to_local(mysql_to_unix($date), 'UP5', true));
}

function mysql_spanish_format($date)
{
    $ci = &get_instance();
    $ci->load->helper('date');
    return date("d-M-Y", mysql_to_unix($date));
}

function format_date($date_mysql)
 {
   $time = strtotime($date_mysql);
   return $myFormatForView = date("d-m-Y", $time);
 }
