<?php
function getChatHtml($p)
{
    if (!isLogged()) return false;
    global $themeData, $user;
    $html = "";
    $messages = getMessages($p);
    
    if (is_array($messages))
    {
        foreach ($messages as $msg)
        {
            $themeData['msg_id'] = $msg['id'];
            $themeData['msg_time'] = $msg['time'];
            $themeData['msg_usr_url'] = $msg['timeline']['url'];
            $themeData['msg_usr_thumb'] = $msg['timeline']['thumbnail_url'];
            $themeData['msg_usr_img'] = $msg['timeline']['avatar_url'];
            $themeData['msg_usr_username'] = $msg['timeline']['username'];
            $themeData['msg_usr_name'] = $msg['timeline']['name'];
            $themeData['msg_txt'] = "";
            
            if (!empty($msg['text']))
            {
                $themeData['txt'] = $msg['text'];
                $themeData['msg_txt'] = \SocialKit\UI::view('chat/txt');
            }

            $themeData['msg_media_url'] = "";
            $themeData['msg_media'] = "";

            if (isset($msg['media']['id']))
            {
                if ($msg['media']['type'] == "photo")
                {
                    $themeData['msg_photo_url'] = SITE_URL . '/' . $msg['media']['each'][0]['complete_url'];
                    $themeData['msg_media'] = \SocialKit\UI::view('chat/photo');
                }
                else if ($msg['media']['type'] == "video")
                {
                    $themeData['msg_vid_url'] = SITE_URL . '/' . $msg['media']['complete_url'];
                    $themeData['msg_vid_name'] = $msg['media']['name'];
                    $themeData['msg_vid_ext'] = $msg['media']['extension'];
                    $themeData['msg_media'] = \SocialKit\UI::view('chat/vid');
                }
                else if ($msg['media']['type'] == "audio")
                {
                    $themeData['msg_audio_url'] = SITE_URL . '/' . $msg['media']['complete_url'];
                    $themeData['msg_audio_name'] = $msg['media']['name'];
                    $themeData['msg_audio_ext'] = $msg['media']['extension'];
                    $themeData['msg_media'] = \SocialKit\UI::view('chat/audio');
                }
                else if ($msg['media']['type'] == "document")
                {
                    $themeData['msg_doc_url'] = SITE_URL . '/' . $msg['media']['complete_url'];
                    $themeData['msg_doc_name'] = $msg['media']['name'];
                    $themeData['msg_doc_ext'] = $msg['media']['extension'];
                    $themeData['msg_media'] = \SocialKit\UI::view('chat/doc');
                }
            }

            $themeData['seen_time'] = "";
            $themeData['msg_seen'] = "";

            if ($msg['timeline']['id'] === $user['id'])
            {
                if ($msg['seen'] > 0)
                {
                    $themeData['seen_time'] = $msg['seen'];
                    $themeData['msg_seen'] = \SocialKit\UI::view('chat/seen');
                }

                $html .= \SocialKit\UI::view('chat/outgoing-text');
            }
            else
            {
                $html .= \SocialKit\UI::view('chat/incoming-text');
            }
        }

        return $html;
    }

    return false;
}