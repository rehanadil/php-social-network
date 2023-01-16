<?php
/* Fix Orientation */
function fixOrientation($path)
{
    if (file_exists ($path))
    {
        if (strrpos($path, '.'))
        {
            $ext = substr($path, strrpos($path,'.') + 1, strlen($path) - strrpos($path, '.'));

            if (in_array($ext, array('jpeg', 'jpg')))
            {
                $fxt = true;
            }
        }
    }

    if (! isset($fxt))
    {
        return false;
    }

    $image = imagecreatefromjpeg($path);
    $exif = exif_read_data($path);
 
    if (!empty($exif['Orientation']))
    {
        switch ($exif['Orientation'])
        {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;
                
            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
    }

    imagejpeg($image, $path);
    return true;
}
