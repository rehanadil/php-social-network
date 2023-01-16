<?php
function getEventInviteList($searchQuery='', $eventId = 0, $limit=5)
{
    global $lang;

    if (! isLogged())
    {
        return array();
    }
    
    global $themeData;
    $invitesHtml = '';

    foreach (getEventInvitesData($searchQuery, $eventId, $limit) as $k => $v)
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($v);
        $timeline = $timelineObj->getRows();
        
        $themeData['list_invite_id'] = $timeline['id'];
        $themeData['list_invite_url'] = $timeline['url'];
        $themeData['list_invite_username'] = $timeline['username'];
        $themeData['list_invite_name'] = $timeline['name'];
        $themeData['list_invite_thumbnail_url'] = $timeline['thumbnail_url'];
        $themeData['list_invite_info'] = "@".$timeline['username'];

        $invitesHtml .= \SocialKit\UI::view('timeline/event/list-invite-each');
    }

    if (!empty($invitesHtml))
    {
        return $invitesHtml;
    }
}
