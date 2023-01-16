<?php
/* Get trendings */
function getTrendings($type='latest', $limit=5)
{
    global $conn, $themeData;
    
    $limit = (int) $limit;
    $oldestUnix = time() - (60 * 60 * 24 * 1);

    if ($limit < 1)
    {
        $limit = 5;
    }
    
    if (empty($type))
    {
        return false;
    }

    if ($type == "latest")
    {
        $query = "SELECT * FROM " . DB_HASHTAGS . " WHERE last_trend_time>$oldestUnix ORDER BY last_trend_time DESC LIMIT $limit";
    }
    elseif ($type == "popular")
    {
        $query = "SELECT * FROM " . DB_HASHTAGS . " WHERE last_trend_time>$oldestUnix ORDER BY trend_use_num DESC LIMIT $limit";
    }
    
    $query = $conn->query($query);
    $trendingsHtml = '';
    
    if ($query->num_rows > 0)
    {
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $fetch['url'] = smoothLink('index.php?a=hashtag&query=' . $fetch['tag']);
            $themeData['list_trend_id'] = $fetch['id'];
            $themeData['list_trend_url'] = $fetch['url'];
            $themeData['list_trend_tag'] = $fetch['tag'];
            
            $trendingsHtml .= \SocialKit\UI::view('trendings/list-each');
        }

        $themeData['list_trendings'] = $trendingsHtml;
        return \SocialKit\UI::view('trendings/content', array(
            'key' => 'trendings_ui_editor',
            'return' => 'string',
            'type' => 'APPEND',
            'content' => array()
        ));
    }
}
