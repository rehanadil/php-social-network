<?php
function getHashtagSearchTemplate($a, $b)
{
    global $themeData;
    $results = getHashtagSearch($a, $b);
    $template = '';

    if (is_array($results))
    {
        foreach ($results as $k => $v)
        {
            $themeData['search_hash'] = $v['hash'];
            $themeData['search_tag'] = $v['tag'];
            $themeData['search_url'] = smoothLink('index.php?a=hashtag&query=' . $v['tag']);

            $template .= \SocialKit\UI::view('header/hashtag-result');
        }
    }

    return $template;
}
