<?php
/* v2.0 */
function __POST__ ($post, $return='')
{
    if ( !isset($_POST[$post]) )
    {
        return $return;
    }
    return $_POST[$post];
}