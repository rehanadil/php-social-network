<?php
/* Create random key */
function generateKey($minlength=5, $maxlength=5, $uselower=true, $useupper=true, $usenumbers=true, $usespecial=false)
{
    $charset = '';
    
    if ($uselower)
    {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    
    if ($useupper)
    {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    
    if ($usenumbers)
    {
        $charset .= "123456789";
    }
    
    if ($usespecial)
    {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    
    if ($minlength > $maxlength)
    {
        $length = mt_rand($maxlength, $minlength);
    }
    else
    {
        $length = mt_rand($minlength, $maxlength);
    }
    
    $key = '';
    
    for ($i = 0; $i < $length; $i++)
    {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    
    return $key;
}
