<?php
function __GET__ ($get, $return='')
{
    if ( !isset($_GET[$get]) )
    {
        return $return;
    }
    return $_GET[$get];
}