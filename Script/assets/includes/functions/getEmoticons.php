<?php
/* Get emoticons */
function getEmoticons()
{
    global $config, $emo;
    $emoticon = array();
    
    if (! isset($emo) or ! is_array($emo))
    {
        return false;
    }
    
    foreach ($emo as $code => $img)
    {
        $emoticon[addslashes($code)] = $config['theme_url'] . '/emoticons/' . $img;
    }
    
    return array_unique($emoticon);
}
