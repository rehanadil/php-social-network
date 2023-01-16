<?php
function file_ext ($Url)
{
    $len = strlen($Url);
    $rpos = strrpos($Url, '.');
    $begin = $len - $rpos;
    $end = $rpos + 1;
    $sub = substr($Url, $end, $begin);
    $lwr = strtolower($sub);
    return $lwr;
}
