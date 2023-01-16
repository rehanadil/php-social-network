<?php
function youtube_search_ajax_request($Data)
{
    $conn = getConnection();
    $escapeObj = new \SocialKit\Escape();

    if (! empty($_GET['a']) && $_GET['a'] == "youtube_search")
    {
        if (! empty($_GET['q']))
        {
            if (preg_match('/^(http\:\/\/|https\:\/\/|www\.|youtube\.com|youtu\.be)/', $_GET['q']))
            {
                return array(
                    'status' => 200,
                    'type' => 'embed'
                );
            }
            else
            {
                $api_url = 'https://www.googleapis.com/youtube/v3/search?safeSearch=strict&type=video&part=snippet&q=' . urlencode($_GET['q']) . '&maxResults=10&key=AIzaSyAM99Mvxg4Mr6zhHS2NxHy-RCiPf2NRJCI';
                $api_content = @file_get_contents($api_url);
                $html = '';
                
                if (! $api_content)
                {
                    $conn->close();
                    return "";
                }
                
                $api_content_array = json_decode($api_content, true);
                
                if (! is_array($api_content_array))
                {
                    $conn->close();
                    return "";
                }
                
                foreach ($api_content_array['items'] as $k => $v)
                {
                    $ytId = $v['id']['videoId'];
                    $ytTitle = $v['snippet']['title'];
                    $ytEncodeTitle = str_replace(array("'", '"'), array("\&apos;", '\&quot;'), $ytTitle);
                    $ytCategory = str_replace(array("'", '"'), array("\&apos;", '\&quot;'), $v['snippet']['channelTitle']);
                    $ytThumbnail = "";

                    if (! empty($v['snippet']['thumbnails']['medium']['url']))
                    {
                        $ytThumbnail = $v['snippet']['thumbnails']['medium']['url'];
                    }

                    $html .= '<div class="api-data-wrapper youtube-api-data" onclick="addYoutubeData(\'' . $ytId . '\',\'' . $ytEncodeTitle . '\');">
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="40px" align="left" valign="middle">
                                <img class="thumbnail" src="' . $ytThumbnail . '" width="32px" height="32px" valign="middle" alt="Youtube">
                            </td>
                            
                            <td align="left" valign="middle">
                                <div class="name">
                                    ' . $ytTitle . '
                                </div>

                                <div class="info">
                                    ' . $ytCategory . '
                                </div>
                            </td>
                        </tr>
                        </table>
                    </div>';
                }
                
                if (! empty($html))
                {
                    return array(
                        'status' => 200,
                        'type' => 'api',
                        'html' => $html
                    );
                }
            }
        }
    }

    return "";
}
\SocialKit\Addons::register('addon_ajax_request', 'youtube_search_ajax_request');