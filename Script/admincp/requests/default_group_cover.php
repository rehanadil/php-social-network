<?php
if (isset($_FILES['cover']))
{
	if (is_uploaded_file($_FILES['cover']['tmp_name']))
	{
		$escapeObj = new \SocialKit\Escape();
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $_FILES['cover']['name']);
        $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        $filename = md5($name . time()) . '.' . $ext;
        if ($_FILES['cover']['size'] > 0)
        {
            if (preg_match('/(jpg|jpeg|png|gif)/', $ext))
            {
                $dir = '../cache/defaults';
             	if (!file_exists($dir)) mkdir($dir, 0777, true);
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $dir . '/' . $filename))
                {
                	$fileUrl = str_replace('../', '', $dir) . '/' . $filename;
                	if ($conn->query("UPDATE " . DB_CONFIGURATIONS . " SET group_default_cover='$fileUrl'"))
                    {
                        $data = array(
                            "status" => 200,
                            "src" => $config['site_url'] . '/' . $fileUrl
                        );
                    }
                }
                chmod($dir, 0755);
            }
        }
	}
}