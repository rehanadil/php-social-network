<?php
function uploadSharedFile($file)
{
    global $conn, $user;

	$filepath = 'uploads/' . date('Y') . '/' . date('m') . '/files';

	if (! file_exists($filepath))
    {
        mkdir($filepath, 0777, true);
    }

    if (is_uploaded_file($file['tmp_name']))
    {
    	$escapeObj = new \SocialKit\Escape();
        $file['name'] = $escapeObj->stringEscape($file['name']);
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $file['name']);
        $extension = readExtension($file['name']);
        $filename = $filepath . '/' . md5(generateKey(5, 20));
        
        if ($file['size'] > 1024)
        {
        	if (preg_match('/(pdf|txt|zip|rar|tar|doc|docx)/', $extension))
            {
            	if (move_uploaded_file($file['tmp_name'], $filename . '.' . $extension))
            	{
                    $query = $conn->query("INSERT INTO " . DB_MEDIA . " (extension,name,type,timeline_id,url,temp,active) VALUES ('$extension','$name','document'," . $user['id'] . ",'$filename',0,1)");
            		return $conn->insert_id;
            	}
            }
        }
    }

    return false;
}
