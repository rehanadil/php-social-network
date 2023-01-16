<?php
require_once('head_tags.php');

function story_youtube_detector($Data)
{
	global $config;
	$story = $Data['data'];

	$rgx0 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)soundcloud.com\/([a-zA-Z0-9_-]+)\/([a-zA-Z0-9_-]+)\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx0, $story['text'], $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$https = $matches[1][$k];
			$author = $matches[3][$k];
			$song = $matches[4][$k];
			$iframe = '<div class="soundcloud-wrapper" align="center"></div>
			<script>
			ifr = document.createElement("iframe");
			ifr.src = \'https://w.soundcloud.com/player/?url=' . $https . 'soundcloud.com/' . $author . '/' . $song . '&amp;color=f07b22\'; 
			ifr.width = "100%";
			ifr.frameBorder = 0;
			$("#story_" + ' . $story['id'] . ').find(".soundcloud-wrapper").html(ifr);
			</script>';
			$story['text'] = str_replace($match, $iframe, $story['text']);
		}
	}

	$rgx1 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)youtube.com\/watch\?v\=([a-zA-Z0-9_-]+)(|\&feature\=youtu\.be|\&t\=([A-Za-z0-9]+))\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx1, $story['text'], $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$yid = $matches[3][$k];
			$iframe = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe>';
			$story['text'] = str_replace($match, $iframe, $story['text']);
		}
	}

	$rgx2 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)youtu.be\/([a-zA-Z0-9_-]+)\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx2, $story['text'], $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$yid = $matches[3][$k];
			$iframe = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe>';
			$story['text'] = str_replace($match, $iframe, $story['text']);
		}
	}

	$rgx3 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)youtube.com\/(embed|v)\/([a-zA-Z0-9_-]+)\" target\=\"\_blank\"\>(.*?)\<\/a\>/i';
	if (preg_match_all($rgx3, $story['text'], $matches))
	{
		foreach ($matches[0] as $k => $match)
		{
			$yid = $matches[4][$k];
			$iframe = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe>';
			$story['text'] = str_replace($match, $iframe, $story['text']);
		}
	}

	$rgx4 = '/\<a class\=\"livepreview\" rel\=\"nofollow\" href\=\"(|https:\/\/|http:\/\/)(|www\.)(.*?)\" target\=\"\_blank\"\>(.*?)\<\/a\>/iu';
	if (preg_match_all($rgx4, $story['text'], $matches))
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

			$preview_data = '<div class="url-preview-container">';

			if (! empty($meta_tags['img_preview']) )
			{
				$preview_data .= '<a href="' . $url . '" target="_blank">
					<div class="img-preview">
						<div style="background-image: url(\'' . $config['site_url'] .'/' . $meta_tags['img_preview'] .'\');"></div>
					</div>
				</a>';
			}
			else {
				$preview_data .= '<a href="' . $url . '" target="_blank">
					<div class="img-preview">
						<div style="background-image: url(\'' . $config['site_url'] .'/addons/add.StoryURLPreview/defaultbg.jpg\');"></div>
					</div>
				</a>';
			}

			if (! empty($meta_tags['title']) )
			{
				$preview_data .= '<div class="title">
					<a href="' . $url . '" target="_blank">' . $meta_tags['title'] . '</a>
				</div>';
			}

			if (! empty($meta_tags['description']) )
			{
				$preview_data .= '<div class="descr">
					<a href="' . $url . '" target="_blank">' . $meta_tags['description'] . '</a>
				</div>';
			}

			$preview_data .= '</div>';
			$story['text'] = str_replace($match, $match . $preview_data, $story['text']);
		}
	}
	
	return $story;
}
\SocialKit\Addons::register('story_content_editor', 'story_youtube_detector');

/* CSS */
function url_preview_css()
{
	return '<style>
	.url-preview-container
	{
		display: block;
		color: #898f9c;
		padding: 5px 10px;
	}
	.url-preview-container .img-preview
	{
		overflow: hidden;
		max-height: 250px;
	}
	.url-preview-container .img-preview div
	{
		background-position: center center;
		background-size: cover;
		background-repeat: no-repeat;
		width: 100%;
		height: 100%;
		padding: 60% 0 0 0;
		overflow: hidden;
	}
	.url-preview-container .title
	{
		color: #333;
		font-size: 15px;
		font-weight: bold;
		margin: 8px 0;
	}
	.url-preview-container .title a
	{
		color: #333;
	}
	.url-preview-container .descr
	{
		color: #555;
		margin: 0 0 8px 0;
	}
	.url-preview-container .descr a, .url-preview-container .descr a:hover
	{
		color: #555;
		text-decoration: none;
	}
	</style>';
}
\SocialKit\Addons::register('head_tags_add_content', 'url_preview_css');