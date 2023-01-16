<?php
/* Get languages */
function getLanguages()
{
    global $conn, $config;
    $get = array();
    $l = array();

    $langQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES);
    
    while ($langFetch = $langQuery->fetch_array(MYSQLI_ASSOC))
    {
        $language = $langFetch['type'];
        $language = str_replace('languages/', '', $language);
        $language = preg_replace('/([A-Za-z]+)\.php/i', '$1', $language);
        
        if ($config['smooth_links'] == 1)
        {
            $language_url = '?lang=' . $language;
        }
        else
        {
            $query_string = $_SERVER['QUERY_STRING'];
            $query_string = preg_replace('/(\&|)lang\=([A-Za-z0-9_]+)/i', '', $query_string);
            $language_url = 'index.php?' . $query_string . '&lang=' . $language;
            $escapeObj = new \SocialKit\Escape();
            $language_url = $escapeObj->stringEscape(strip_tags($language_url));
        }
        
        $l[$language] = $language_url;
    }

    return $l;
}
