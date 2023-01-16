<?php
function grab_meta_tags ($Url)
{
    $Urlname = preg_replace('/[^A-Za-z0-9_]/i', '', $Url);
    $get_meta = get_meta_tags($Url);
    $get_html = file_get_contents($Url);
    $title_preg_match = preg_match('/\<title\>(.*?)\<\/title\>/i', $get_html, $title_match);

    if ( !empty($title_match[1]) )
    {
        $get_meta['title'] = $title_match[1];
    }

    $image = file_get_contents("https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url=$Url&screenshot=true&key=AIzaSyDXBJtT85FNi-4es98tVgN_MB2Ao60DmaQ");
    $image = json_decode($image, true);
    $image = $image['screenshot']['data'];
    $image = str_replace(array('_','-'), array('/','+'), $image); 
    $get_meta['img_preview'] = $image;


    return $get_meta;
}