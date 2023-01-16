<?php
function getTrendPosts($tag='', $afterId = 0)
{
	global $conn, $escapeObj;
	$searchQuery = str_replace('#', '', $escapeObj->stringEscape($tag));
    $hashdata = getHashtag($searchQuery);
    $afterId = (int) $afterId;
	
    if (is_array($hashdata) && count($hashdata) > 0)
    {
        $search_string = "#[" . $hashdata['id'] . "]";
        $sql = "SELECT id FROM " . DB_POSTS . " WHERE text LIKE '%$search_string%' AND hidden=0 AND active=1";

        if ($afterId > 0) $sql .= " AND id<$afterId";

        $sql .= " ORDER BY id DESC LIMIT 10";

        $query = $conn->query($sql);
        $storiesHtml = '';
        
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $storyObj = new \SocialKit\Story();
            $storyObj->setId($fetch['id']);
            $story = $storyObj->getRows();

            if (isset($story['id']))
            {
                $storiesHtml .= $storyObj->getTemplate();
            }
        }

        return $storiesHtml;
    }

    return false;
}