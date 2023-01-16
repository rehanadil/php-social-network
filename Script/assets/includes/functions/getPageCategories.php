<?php
/* Get page categories */
function getPageCategories($catId=0, $check_only=false) {
    if (! isLogged()) {
        return array();
    }
    
    global $conn;
    $get = array();
    $catId = (int) $catId;
    
    if ($check_only == true)
    {
        $query = $conn->query("SELECT id FROM " . DB_PAGE_CATEGORIES . " WHERE id=$catId AND active=1");
        return $query->num_rows;
    }
    else
    {
        $query = $conn->query("SELECT id,category_id,name FROM " . DB_PAGE_CATEGORIES . " WHERE category_id=$catId AND active=1");
        
        while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
        {
            $get[] = $fetch;
        }
    }
    
    return $get;
}
