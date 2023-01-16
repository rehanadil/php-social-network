<?php
/* Register Media */
function registerMedia($upload, $album_id=0)
{
    if (!isLogged()) return false;
    
    global $conn, $user;
    $photo_dir = 'uploads/photos/' . date('Y') . '/' . date('m');
    
    if (!file_exists($photo_dir))
    {
        mkdir($photo_dir, 0777, true);
    }
    
    if (is_uploaded_file($upload['tmp_name']))
    {
        $escapeObj = new \SocialKit\Escape();
        $upload['name'] = $escapeObj->stringEscape($upload['name']);
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $upload['name']);
        $ext = strtolower(substr($upload['name'], strrpos($upload['name'], '.') + 1, strlen($upload['name']) - strrpos($upload['name'], '.')));
        
        if ($upload['size'] > 1024)
        {
            if (preg_match('/(jpg|jpeg|png|gif)/', $ext))
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
                        @fixOrientation($original_file);

                        $min_size = $width;
                        
                        if ($width > $height)
                        {
                            $min_size = $height;
                        }
                        
                        $min_size = floor($min_size);
                        
                        if ($min_size > 920)
                        {
                            $min_size = 920;
                        }
                        
                        $imageSizes = array(
                            'thumb' => array(
                                'type' => 'crop',
                                'width' => 64,
                                'height' => 64,
                                'name' => $original_file_name . '_thumb'
                            ),
                            '100x100' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => $min_size,
                                'name' => $original_file_name . '_100x100'
                            ),
                            '100x75' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => floor($min_size * 0.75),
                                'name' => $original_file_name . '_100x75'
                            )
                        );
                        
                        foreach ($imageSizes as $ratio => $data)
                        {
                            $save_file = $data['name'] . '.' . $ext;
                            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
                        }
                        

                        $newResizeWidth = $width;
                        if ($width > 920)
                        {
                            $newResizeWidth = 920;
                        }
                        processMedia('resize', $original_file, $original_file, $newResizeWidth, 0);


                        $conn->query("UPDATE " . DB_MEDIA . " SET timeline_id=" . $user['id'] . ",album_id=$album_id,url='$original_file_name',temp=0,active=1 WHERE id=$sql_id");

                        $get = array(
                            'id' => $sql_id,
                            'active' => 1,
                            'extension' => $ext,
                            'name' => $name,
                            'url' => $original_file_name
                        );
                        
                        return $get;
                    }
                }
            }
        }
    }
}
