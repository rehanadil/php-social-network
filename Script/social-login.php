<?php
require_once('assets/includes/core.php');

// Social Site
if (! empty($_GET['site']))
{
    $site = $_GET['site'];
    $escapeObj = new \SocialKit\Escape();
    
    // Facebook
    if ($site == "facebook")
    {
        require_once('library/facebook.php');
    }

    // Google
    if ($site == "google")
    {
        require_once('library/google.php');
    }

    // Twitter
    if ($site == "twitter")
    {
        require_once('library/twitter.php');
    }

    // Instagram
    if ($site == "instagram")
    {
        require_once('library/instagram.php');
    }
}

function is_username_taken ($u='')
{
    if (empty($u))
    {
        return false;
    }

    global $conn;
    $escapeObj = new \SocialKit\Escape();
    $u = $escapeObj->stringEscape($u);
    $query = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE username='$u'");

    if ($query->num_rows == 1)
    {
        return true;
    }

    return false;
}

function import_photos($url='', $decidedExt=false)
{
    global $conn;
    
    if (empty($url)) return false;
    if (($source = @file_get_contents($url)) == false) return false;

    $photo_dir = 'uploads/photos/' . date('Y') . '/' . date('m');
    
    if (!file_exists($photo_dir)) mkdir($photo_dir, 0777, true);
    
    $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $url);
    $url_ext = $url;
    
    if (($qs_ext_pos = strrpos($url, '?')) !== false) {
        $url_ext = substr($url, 0, $qs_ext_pos);
    }
    
    $dot_ext_pos = strrpos($url_ext, '.');
    $url_ext = strtolower(substr($url_ext, $dot_ext_pos + 1, strlen($url_ext) - $dot_ext_pos));

    if ($decidedExt)
    {
        $url_ext = "jpg";
    }
    
    if (! preg_match('/^(jpg|jpeg|png)$/', $url_ext)) {
        return false;
    }
    
    $query = $conn->query("INSERT INTO " . DB_MEDIA . " (extension,name,type) VALUES ('$url_ext','$name','photo')");
    
    if (! $query)
    {
        return false;
    }
    
    $sqlId = $conn->insert_id;
    $original_file_name = $photo_dir . '/' . generateKey() . '_' . $sqlId . '_' . md5($sqlId);
    $original_file = $original_file_name . '.' . $url_ext;
    $register_cover = @file_put_contents($original_file, $source);
    
    if ($register_cover)
    {
        list($width, $height) = getimagesize($original_file);
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
            $save_file = $data['name'] . '.' . $url_ext;
            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
        }
        
        $conn->query("UPDATE " . DB_MEDIA . " SET url='$original_file_name',temp=0,active=1 WHERE id=$sqlId");
        createCover($sqlId);
        
        $get = array(
            'id' => $sqlId,
            'active' => 1,
            'extension' => $url_ext,
            'name' => $name,
            'url' => $original_file_name
        );
        
        return $get;
    }
}

?>