<?php
/* Register cover */
function registerCoverImage($upload, $pos=0)
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn;
    
    if (! file_exists('uploads/photos/' . date('Y') . '/' . date('m')))
    {
        mkdir('uploads/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    
    $photo_dir = 'uploads/photos/' . date('Y') . '/' . date('m');
    
    if (is_uploaded_file($upload['tmp_name']))
    {
        $escapeObj = new \SocialKit\Escape();
        $upload['name'] = $escapeObj->stringEscape($upload['name']);
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $upload['name']);
        $ext = strtolower(substr($upload['name'], strrpos($upload['name'], '.') + 1, strlen($upload['name']) - strrpos($upload['name'], '.')));
        
        if ($upload['size'] > 1024)
        {
            if (preg_match('/(jpg|jpeg|png)/', $ext))
            {
                list($width, $height) = getimagesize($upload['tmp_name']);
                
                $query = $conn->query("INSERT INTO " . DB_MEDIA . " (extension,name,type) VALUES ('$ext','$name','photo')");
                
                if ($query)
                {
                    $sql_id = $conn->insert_id;
                    $original_file_name = $photo_dir . '/' . generateKey() . '_' . $sql_id . '_' . md5($sql_id);
                    $original_file = $original_file_name . '.' . $ext;
                    
                    if (move_uploaded_file($upload['tmp_name'], $original_file))
                    {
                        processMedia('resize', $original_file, $original_file, $width, 0, 100);

                        $img = $original_file;
                        $cover_img_url = $original_file_name . '_cover.' . $ext;
                        $dst_x = 0;
                        $dst_y = 0;
                        $src_x = 0;
                        $src_y = 0;
                        $dst_w = $width;
                        $dst_h = $dst_w * (0.3);
                        $src_w = $width;
                        $src_h = $dst_h;
                        
                        if (! empty($pos) && is_numeric($pos) && $pos < $width)
                        {
                            $pos = $escapeObj->stringEscape($pos);
                            $src_y = $width * $pos;
                        }
                        
                        $cover_img = imagecreatetruecolor($dst_w, $dst_h);

                        if ($ext == "png")
                        {
                            $image = imagecreatefrompng($img);
                        }
                        else
                        {
                            $image = imagecreatefromjpeg($img);
                        }
                        
                        imagecopyresampled($cover_img, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                        imagejpeg($cover_img, $cover_img_url, 100);
                        
                        $conn->query("UPDATE " . DB_MEDIA . " SET url='$original_file_name',temp=0,active=1 WHERE id=$sql_id");

                        $get = array(
                            'id' => $sql_id,
                            'active' => 1,
                            'extension' => $ext,
                            'name' => $name,
                            'url' => $original_file_name,
                            'cover_url' => $original_file_name . '_cover.' . $ext
                        );
                        
                        return $get;
                    }
                }
            }
        }
    }
}
