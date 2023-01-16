<?php
function createCover($cover_id=0, $pos=0)
{
    if (! isLogged()) {
        return false;
    }
    
    global $conn;
    $cover_id = (int) $cover_id;
    
    $query = $conn->query("SELECT * FROM " . DB_MEDIA . " WHERE id=$cover_id");
    
    if ($query->num_rows == 1)
    {
        $cover = $query->fetch_array(MYSQLI_ASSOC);
        $img = $cover['url'] . '.' . $cover['extension'];
        $cover_img_url = $cover['url'] . '_cover.' . $cover['extension'];

        list($width, $height) = getimagesize($img);
        $dst_x = 0;
        $dst_y = 0;
        $src_x = 0;
        $src_y = 0;
        $dst_w = $width;
        $dst_h = $dst_w * (0.3);
        $src_w = $width;
        $src_h = $dst_h;
        
        if (!empty($pos) && is_numeric($pos) && $pos < $width)
        {
            $escapeObj = new \SocialKit\Escape();
            $pos = $escapeObj->stringEscape($pos);
            $src_y = $width * $pos;
        }
        
        $cover_img = imagecreatetruecolor($dst_w, $dst_h);
        
        if ($cover['extension'] == "png")
        {
            $image = imagecreatefrompng($img);
        }
        else
        {
            $image = imagecreatefromjpeg($img);
        }

        imagecopyresampled($cover_img, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        imagejpeg($cover_img, $cover_img_url, 100);
        return $cover_img_url;
    }
}
