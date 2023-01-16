<?php
/* Hashtags */
function getHashtag($tag='', $update=true)
{
    global $conn;
    $create = false;
    $escapeObj = new \SocialKit\Escape();
    $tag = $escapeObj->stringEscape($tag);
    
    if (empty($tag))
    {
        return false;
    }
    
    if (is_numeric($tag))
    {
        $query = $conn->query("SELECT * FROM " . DB_HASHTAGS . " WHERE id=$tag");
    }
    else
    {
        $query = $conn->query("SELECT * FROM " . DB_HASHTAGS . " WHERE tag='$tag'");
        $create = true;
    }
    
    if ($query->num_rows == 1)
    {
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        
        $trendNum = $fetch['trend_use_num'] + 1;
        $conn->query("UPDATE " . DB_HASHTAGS . " SET trend_use_num=$trendNum WHERE id=" . $fetch['id']);

        if ($update == true)
        {
            $conn->query("UPDATE " . DB_HASHTAGS . " SET last_trend_time=". time() . " WHERE id=" . $fetch['id']);
        }

        return $fetch;
    }
    elseif ($query->num_rows == 0)
    {
        if ($create == true)
        {
            $hash = md5($tag);
            $query2 = $conn->query("INSERT INTO " . DB_HASHTAGS . " (hash,tag,last_trend_time) VALUES ('$hash','$tag'," . time() . ")");
            
            if ($query2)
            {
                $sqlId = $conn->insert_id;
                $get = array(
                    'id' => $sqlId,
                    'hash' => $hash,
                    'tag' => $tag,
                    'last_trend_time' => time(),
                    'trend_use_num' => 0
                );

                return $get;
            }
        }
    }
}
