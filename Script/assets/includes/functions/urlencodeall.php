<?php
function urlencodeall($x)
{
    $out = '';
    for ($i = 0; isset($x[$i]); $i++)
    {
        $c = $x[$i];
        if (!ctype_alnum($c)) $c = '%' . sprintf('%02X', ord($c));
        $out .= $c;
    }
    return $out;
}