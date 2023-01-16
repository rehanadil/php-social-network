<?php
/* Register notifications */
function registerNotification($data=array())
{
    if (! isLogged())
    {
        return false;
    }
    
    global $conn;
    $escapeObj = new \SocialKit\Escape();
    
    if (! isset($data['recipient_id']) or ! is_numeric($data['recipient_id']))
    {
        return false;
    }
    
    if (empty($data['post_id']))
    {
        $data['post_id'] = 0;
    }
    
    if (! is_numeric($data['post_id']))
    {
        return false;
    }

    $recipientId = (int) $data['recipient_id'];
    $postId = (int) $data['post_id'];
    
    if (empty($data['notifier_id']))
    {
        global $user, $userObj;
        $notifierId = $user['id'];
        $notifierObj = $userObj;
        $notifier = $user;
    }
    else
    {
        $notifierId = (int) $data['notifier_id'];
        $notifierObj = new \SocialKit\User();
        $notifierObj->setId($notifierId);
        $notifier = $notifierObj->getRows();
    }
    
    if (! $notifierObj->isAdmin())
    {
        return false;
    }
    
    if ($recipientId == $notifierId)
    {
        return false;
    }
    
    if (empty($data['text']))
    {
        return false;
    }
    
    if (empty($data['type']))
    {
        return false;
    }
    
    if (empty($data['url']))
    {
        return false;
    }
    
    $text = $escapeObj->stringEscape($data['text']);
    $type = $escapeObj->stringEscape($data['type']);
    $url = $data['url'];

    $text = strip_tags($text);

    $recipientObj = new \SocialKit\User();
    $recipientObj->setId($recipientId);
    $recipient = $recipientObj->getRows();
    
    if (! isset($recipient['id']))
    {
        return false;
    }

    $lang = array();
    $langQuery = $this->getConnection()->query("SELECT keyword,text FROM " . DB_LANGUAGES . " WHERE type='" . $recipient['language'] . "'");
    
    while($langFetch = $langQuery->fetch_array(MYSQLI_ASSOC))
    {
        $lang[$langFetch['keyword']] = $langFetch['text'];
    }
    
    $query = $conn->query("SELECT id FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$recipientId AND post_id=$postId AND type='$type' AND active=1");
    
    if ($query->num_rows > 0)
    {
        $query2 = $conn->query("DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=$recipientId AND post_id=$postId AND type='$type' AND active=1");
    }
    
    if (! isset($data['undo']) or $data['undo'] != true)
    {
        $query3 = $conn->query("INSERT INTO " . DB_NOTIFICATIONS . " (timeline_id,active,notifier_id,post_id,text,time,type,url) VALUES ($recipientId,1,$notifierId,$postId,'$text'," . time() . ",'$type','$url')");
        
        if ($query3)
        {
            return true;
        }
    }
}
