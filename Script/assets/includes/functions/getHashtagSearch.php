<?php
function getHashtagSearch($tag='', $limit=4)
{
    global $conn;
    $get = array();
    $escapeObj = new \SocialKit\Escape();
    $tag = $escapeObj->stringEscape($tag);
    $limit = (int) $limit;
    
    if (empty($tag))
    {
        return false;
    }

    $tag = str_replace('#', '', $tag);
    
    if ($limit < 1)
    {
        $limit = 5;
    }
    
    if (is_numeric($tag))
    {
        $query = $conn->query("SELECT * FROM " . DB_HASHTAGS . " WHERE id=$tag LIMIT $limit");
    }
    else
    {
        $query = $conn->query("SELECT * FROM " . DB_HASHTAGS . " WHERE tag LIKE _utf8'%$tag%' collate utf8_general_ci LIMIT $limit");
    }
    
    if ($query->num_rows > 0)
    {
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $get[] = $fetch;
        }
        
        return $get;
    }
}
