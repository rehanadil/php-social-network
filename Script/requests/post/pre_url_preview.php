<?php
if (isset($_POST['text']))
{
	$text = $_POST['text'];
	
	$text = $escapeObj->createLinks($text);
    $text = $escapeObj->createHashtags($text);
    $text = $escapeObj->postEscape($text);

    $text = $escapeObj->getLinks($text);
    
    $previewHtml = "";

    $rgx4 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)(.*?)\" target\=\"\_blank\"\>(.*?)\<\/a\>/iu';
	if (preg_match_all($rgx4, $text, $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$url = $matches[1][$k] . $matches[2][$k] . $matches[3][$k];
			$urlname = preg_replace('/[^A-Za-z0-9_]/i', '', $url);
			$metadir = 'uploads/urlpreviews/' . date('Y') . '/' . date('m');
			$metafile = $metadir . '/' . $urlname . '.json';
			if (! file_exists($metadir) )
			{
				mkdir($metadir, 0777, true);
			}

			if ( file_exists($metafile) )
			{
				$meta_data = file_get_contents($metafile);
				$meta_tags = json_decode($meta_data, true);
			}
			else
			{
				$escapeObj = new \SocialKit\Escape();
				$get_meta_tags = grab_meta_tags($url);
				$meta_tags = array();
				$meta_tags['title'] = $escapeObj->stringEscape($get_meta_tags['title']);
				
				$meta_tags['img_preview'] = $get_meta_tags['img_preview'];
				$imgdata = base64_decode($meta_tags['img_preview']);
				$im = imagecreatefromstring($imgdata);
				if ($im !== false)
				{
					$imgfile = $metadir . '/' . $urlname . '.png';
				    imagepng($im, $imgfile);
				    imagedestroy($im);
				    $meta_tags['img_preview'] = $imgfile;
				}

				if ( isset($get_meta_tags['description']) )
				{
					$meta_tags['description'] = $escapeObj->stringEscape($get_meta_tags['description']);
				}

				file_put_contents($metafile, json_encode($meta_tags));
			}

			$previewHtml = '<div class="url-preview-container">';

			if (! empty($meta_tags['img_preview']) )
			{
				$previewHtml .= '<a href="' . $url . '" target="_blank">
					<div class="img-preview">
						<div style="background-image: url(\'' . $config['site_url'] .'/' . $meta_tags['img_preview'] .'\');"></div>
					</div>
				</a>';
			}
			else {
				$previewHtml .= '<a href="' . $url . '" target="_blank">
					<div class="img-preview">
						<div style="background-image: url(\'' . $config['site_url'] .'/addons/add.StoryURLPreview/defaultbg.jpg\');"></div>
					</div>
				</a>';
			}

			if (! empty($meta_tags['title']) )
			{
				$previewHtml .= '<div class="title">
					<a href="' . $url . '" target="_blank">' . $meta_tags['title'] . '</a>
				</div>';
			}

			if (! empty($meta_tags['description']) )
			{
				$previewHtml .= '<div class="descr">
					<a href="' . $url . '" target="_blank">' . $meta_tags['description'] . '</a>
				</div>';
			}

			$previewHtml .= '</div>';
		}
	}

	$rgx3 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)youtube.com\/(embed|v)\/([a-zA-Z0-9_-]+)\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx3, $text, $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$yid = $matches[4][$k];
			$previewHtml = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe>';
		}
	}

	$rgx2 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)youtu.be\/([a-zA-Z0-9_-]+)\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx2, $text, $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$yid = $matches[3][$k];
			$previewHtml = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe>';
		}
	}

	$rgx1 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)youtube.com\/watch\?v\=([a-zA-Z0-9_-]+)(|\&feature\=youtu\.be|\&t\=([A-Za-z0-9]+))\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx1, $text, $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$yid = $matches[3][$k];
			$previewHtml = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe>';
		}
	}

	$rgx0 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)soundcloud.com\/([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx0, $text, $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$https = $matches[1][$k];
			$author = $matches[3][$k];
			$song = $matches[4][$k];
			$previewHtml = '<div class="soundCloudPreview" align="center"></div>
			<script>
			ifr = document.createElement("iframe");
			ifr.src = \'https://w.soundcloud.com/player/?url=' . $https . 'soundcloud.com/' . $author . '/' . $song . '&amp;color=f07b22\'; 
			ifr.width = "100%";
			ifr.frameBorder = 0;
			$(".soundCloudPreview").html(ifr);
			</script>';
		}
	}

	if (! empty($previewHtml))
	{
		$data = array(
		    'status' => 200,
		    'html' => $previewHtml
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();