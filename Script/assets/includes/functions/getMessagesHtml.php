<?php
function getMessagesHtml($p)
{
    if (!isLogged()) return false;
    global $themeData, $user;
    $html = "";
    $messages = getMessages($p);
    
    if (is_array($messages))
    {
        foreach ($messages as $msg)
        {
            $themeData['message_id'] = $msg['id'];
            $themeData['message_time'] = $msg['time'];
            $themeData['message_timeline_url'] = $msg['timeline']['url'];
            $themeData['message_timeline_thumbnail_url'] = $msg['timeline']['thumbnail_url'];
            $themeData['message_timeline_avatar_url'] = $msg['timeline']['avatar_url'];
            $themeData['message_timeline_name'] = $msg['timeline']['name'];
            $themeData['message_text'] = "";
            
            if (!empty($msg['text']))
            {
                $themeData['text'] = $msg['text'];
                $themeData['message_text'] = \SocialKit\UI::view('messages/text');
            }

            $themeData['message_media_url'] = "";
            $themeData['message_media'] = "";

            if (isset($msg['media']['id']))
            {
                if ($msg['media']['type'] == "photo")
                {
                    $themeData['message_photo_url'] = SITE_URL . '/' . $msg['media']['each'][0]['complete_url'];
                    $themeData['message_media'] = \SocialKit\UI::view('messages/photo');
                }
                else if ($msg['media']['type'] == "video")
                {
                    $themeData['message_video_url'] = SITE_URL . '/' . $msg['media']['complete_url'];
                    $themeData['message_video_name'] = $msg['media']['name'];
                    $themeData['message_video_extension'] = $msg['media']['extension'];
                    $themeData['message_media'] = \SocialKit\UI::view('messages/video');
                }
                else if ($msg['media']['type'] == "audio")
                {
                    $themeData['message_audio_url'] = SITE_URL . '/' . $msg['media']['complete_url'];
                    $themeData['message_audio_name'] = $msg['media']['name'];
                    $themeData['message_audio_extension'] = $msg['media']['extension'];
                    $themeData['message_media'] = \SocialKit\UI::view('messages/audio');
                }
                else if ($msg['media']['type'] == "document")
                {
                    $themeData['message_document_url'] = SITE_URL . '/' . $msg['media']['complete_url'];
                    $themeData['message_document_name'] = $msg['media']['name'];
                    $themeData['message_document_extension'] = $msg['media']['extension'];
                    $themeData['message_media'] = \SocialKit\UI::view('messages/document');
                }
            }

            $themeData['seen_time'] = "";
            $themeData['message_seen'] = "";

            if ($msg['admin'] == true)
            {
                if ($msg['seen'] > 0
                    && $user['subscription_plan']['last_seen'] == 1)
                {
                    $themeData['seen_time'] = $msg['seen'];
                    $themeData['message_seen'] = \SocialKit\UI::view('messages/seen');
                }

                $html .= \SocialKit\UI::view('messages/outgoing-message');
            }
            else
            {
                $html .= \SocialKit\UI::view('messages/incoming-message');
            }
        }

        return $html;
    }

    return false;
}