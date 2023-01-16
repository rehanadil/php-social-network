<?php
/* Get page category data */
function getPageCategoryData($catId=0)
{
    global $conn, $lang;
    $catId = (int) $catId;
    $query = $conn->query("SELECT * FROM " . DB_PAGE_CATEGORIES . " WHERE id=$catId AND active=1");
    
    if ($query->num_rows == 1)
    {
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        $fetch['key'] = $fetch['name'];
        $fetch['name'] = $lang[$fetch['name']];
        return $fetch;
    }
}
