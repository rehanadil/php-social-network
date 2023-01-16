<?php
/* Send Message */
function registerMessage($data=array())
{
    if (! isLogged()) {
        return false;
    }
    
    global $conn, $config, $lang;
    $post_ability = false;
    $other_media = false;

    if (empty($data['timeline_id']) or ! is_numeric($data['timeline_id']) or $data['timeline_id'] < 1)
    {
        global $user, $userObj;
        $timelineId = $user['id'];
        $timelineObj = $userObj;
        $timeline = $user;
    }
    else
    {
        $timelineId = (int) $data['timeline_id'];
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
    }

    if ($timeline['type'] == "group")
    {
        return false;
    }

    if (! $timelineObj->isAdmin())
    {
        return false;
    }
    
    $text = '';
    $media_id = 0;
    $soundcloud_uri = '';
    $soundcloud_title = '';
    $youtube_video_id = '';
    $youtube_title = '';
    $google_map_name = '';
    $recipient_id = 0;
    $type1 = $data['type'];
    $type2 = 'none';
    
    if (!empty($data['text']))
    {
        $text = $data['text'];
        
        if ($config['message_character_limit'] > 0)
        {
            if (strlen($data['text']) > $config['message_character_limit'])
            {
                return false;
            }
        }

        $escapeObj = new \SocialKit\Escape();

        // Links
        $text = $escapeObj->createLinks($text);

        // Hashtags
        $text = $escapeObj->createHashtags($text);

        // Mentions
        $mentions = $escapeObj->createMentions($text);
        $text = $mentions['content'];
        
        $text = $escapeObj->postEscape($text);

        $post_ability = true;
    }
    
    if (!empty($data['recipient_id']) && is_numeric($data['recipient_id']) && $data['recipient_id'] > 0)
    {
        $recipientId = (int) $data['recipient_id'];
    }
    
    if ($recipientId > 0)
    {
        $recipientObj = new \SocialKit\User();
        $recipientObj->setId($recipientId);
        $recipient = $recipientObj->getRows();
        
        if (empty($recipient['id']))
        {
            return false;
        }
        
        if ($timelineId == $recipientId)
        {
            return false;
        }
        
        if ($recipient['type'] == "user" && $timeline['type'] != "page")
        {
            if ($recipient['message_privacy'] != "everyone" && ! $recipientObj->isFollowing())
            {
                return false;
            }
        }
        elseif ($recipient['type'] == "page")
        {
            if ($recipient['message_privacy'] != "everyone")
            {
                return false;
            }
        }
        elseif ($recipient['type'] == "group")
        {
            return false;
        }
    }

    if (isset($data['photos']['name']))
    {
        $photoCount = count($data['photos']['name']);

        if ($photoCount == 1)
        {
            $photo_param = array(
                'tmp_name' => $data['photos']['tmp_name'][0],
                'name' => $data['photos']['name'][0],
                'size' => $data['photos']['size'][0]
            );
            $photo_data = registerMedia($photo_param);
            
            if (isset($photo_data['id']))
            {
                $media_id = $photo_data['id'];
                $other_media = true;
                $post_ability = true;
            }
        }
        else
        {
            $query = $conn->query("INSERT INTO " . DB_MEDIA . " (timeline_id,active,name,type) VALUES ($timelineId,1,'temp_" . generateKey() . "','album')");
            
            if ($query)
            {
                $mediaId = $conn->insert_id;
                
                for ($i = 0; $i < $photoCount; $i++)
                {
                    $photo_param = array(
                        'tmp_name' => $data['photos']['tmp_name'][$i],
                        'name' => $data['photos']['name'][$i],
                        'size' => $data['photos']['size'][$i]
                    );
                    $photo_data = registerMedia($photo_param, $media_id);
                    
                    if (! empty($photo_data['id']))
                    {
                        $query2 = $conn->query("INSERT INTO " . DB_MESSAGES . " (active,media_id,time,timeline_id,recipient_id) VALUES (1," . $photo_data['id'] . "," . time() . "," . $timelineId . ",$recipientId)");
                        
                        if ($query2)
                        {
                            $mediaPostId = $conn->insert_id;
                            
                            $conn->query("UPDATE " . DB_MEDIA . " SET post_id=$mediaPostId WHERE id=" . $photo_data['id']);

                            $mediaPostObj = new \SocialKit\Story();
                            $mediaPostObj->setId($mediaPostId);
                            $mediaPost = $mediaPostObj->getRows();

                            $mediaPostObj->putFollow();
                        }
                    }
                }
                
                $other_media = true;
                $post_ability = true;
            }
        }
    }
    
    if ($post_ability)
    {
        $query = $conn->query("INSERT INTO " . DB_MESSAGES . " (active,media_id,text,time,timeline_id,recipient_id) VALUES (1,$media_id,'$text'," . time() . ",$timelineId,$recipientId)");

        if ($query)
        {
            $post_id = $conn->insert_id;
            $query2 = $conn->query("UPDATE " . DB_MESSAGES . " SET post_id=$post_id WHERE id=$post_id");

            /* E-mail notification */
            if ($recipient['type'] == "user")
            {
                if ($recipient['mailnotif_message'] == true)
                {
                    global $themeData;
                    $themeData['conversation_url'] = smoothLink('index.php?a=messages&recipient_id=' . $timeline['username']);
                    $themeData['mail_recipient_name'] = $recipient['name'];
                    $themeData['mail_generator_url'] = $timeline['url'];
                    $themeData['mail_generator_name'] = $timeline['name'];
                    $themeData['mail_generator_avatar'] = $timeline['thumbnail_url'];
                    
                    $subject = str_replace("{user}", $timeline['name'] . " (@" . $timeline['username'] . ")", $lang['new_message_email_subject']);

                    $message = \SocialKit\UI::view('emails/notifications/new-message');
                    send_mail($recipient['email'], $subject, $message);
                }
            }
            
            return $post_id;
        }
    }
}
