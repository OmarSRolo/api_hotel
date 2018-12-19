<?php
function api_url($str = "")
{
    return str_replace("api/", "api/" . $str, substr(base_url(), stripos('api', base_url())));
}