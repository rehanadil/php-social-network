<?php
/* Story postbox */
function getStoryPostBox($timelineId=0, $recipientId=0, $placeholder='') /* Publisher, Wall Owner */
{
    if (!isLogged()) return false;
    
    global $themeData, $lang, $config, $user, $funcGetSkemojiHtml;
    $continue = true;
    $timelineId = (int) $timelineId;
    $recipientId = (int) $recipientId;

    if ($timelineId < 1) $timelineId = $user['id'];
    
    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($timelineId);
    $timeline = $timelineObj->getRows();

    if (!$timelineObj->isAdmin()) return false;

    $themeData['publisher_id'] = $timeline['id'];

    if ($recipientId < 1) $recipientId = 0;
    
    if ($timelineId == $recipientId) $recipientId = 0;
    
    if ($recipientId > 0)
    {
        $recipientObj = new \SocialKit\User();
        $recipientObj->setId($recipientId);
        $recipient = $recipientObj->getRows();
        
        if (!isset($recipient['id'])) return false;

        if ($recipient['type'] === "user")
        {
            if ($recipient['timeline_post_privacy'] === "following")
            {
                if (!$recipientObj->isFollowedBy($timelineId)) $continue = false;
            }
            elseif ($recipient['timeline_post_privacy'] === "none") $continue = false;
        }
        elseif ($recipient['type'] === "page")
        {
            if ($recipient['timeline_post_privacy'] !== "everyone")
            {
                if (!$recipientObj->isPageAdmin()) $continue = false;
            }
        }
        elseif ($recipient['type'] === "group")
        {
            if ($recipient['timeline_post_privacy'] === "members")
            {
                $continue = false;

                if ($recipientObj->isFollowedBy() || $recipientObj->isGroupAdmin()) $continue = true;
            }
            elseif ($recipient['timeline_post_privacy'] === "admins")
            {
                if (!$recipientObj->isGroupAdmin()) $continue = false;
            }
        }
        
        $themeData['recipient_id'] = $recipient['id'];
    }

    if ($recipientId == 0
        && $timeline['type'] === "user")
    {
        $themeData['create_album_url'] = smoothLink('index.php?a=album&b=create');
        $themeData['story_publisher_header'] = \SocialKit\UI::view('story/publisher-box/header/header2');
    }
    else
    {
        $themeData['story_publisher_header'] = \SocialKit\UI::view('story/publisher-box/header/header1');
    }
    
    $themeData['friends_label'] = $lang['friends_label'];

    if ($config['friends'] != 1) $themeData['friends_label'] = $lang['people_i_follow'];

    if (empty($placeholder))
    {
        $placeholder = $lang['post_textarea_label'];
        $themeData['postbox_placeholder'] = $placeholder;
    }

    if ($config['story_allow_privacy'] == 1)
    {
        if ($config['friends'] == 1)
        {
            $themeData['following_privacy_title'] = $lang['friends_user'];
            $themeData['following_privacy_description'] = $lang['friends_privacy_description'];
        }
        else
        {
            $themeData['following_privacy_title'] = $lang['following_user'];
            $themeData['following_privacy_description'] = $lang['following_privacy_description'];
        }

        if ($recipientId == 0
            && $timeline['type'] === "user") $themeData['privacy_selector'] = \SocialKit\UI::view('story/publisher-box/privacy-selector');
    }

    /* Wrappers */
    $themeData['textarea_wrapper'] = \SocialKit\UI::view('story/publisher-box/wrappers/textarea');

    if ($config['story_allow_photo_upload'] == 1) $themeData['photos_wrapper'] = \SocialKit\UI::view('story/publisher-box/wrappers/photos');

    $themeData['googlemaps_wrapper'] = \SocialKit\UI::view('story/publisher-box/wrappers/googlemaps');

    if ($user['subscription_plan']['use_emoticons'] == 1) $themeData['emoticons_wrapper'] = \SocialKit\UI::view('story/publisher-box/wrappers/emoticons');

    if ($config['story_allow_addons'] == 1)
    {
        $themeData['all_wrappers'] = \SocialKit\UI::view('story/publisher-box/wrappers/all', array(
            'key' => 'new_story_feature_option',
            'return' => 'string',
            'type' => 'APPEND',
            'content' => array()
        ));
    }
    else
    {
        $themeData['all_wrappers'] = \SocialKit\UI::view('story/publisher-box/wrappers/all');
    }

    /* Launcher Icons */
    if ($config['story_allow_photo_upload'] == 1)
        $themeData['photos_launcher_icon'] = \SocialKit\UI::view('story/publisher-box/launcher-icons/photos');
    
    $themeData['googlemaps_launcher_icon'] = \SocialKit\UI::view('story/publisher-box/launcher-icons/googlemaps');

    if ($user['subscription_plan']['use_emoticons'] == 1) $themeData['emoticons_launcher_icon'] = \SocialKit\UI::view('story/publisher-box/launcher-icons/emoticons');

    if ($config['story_allow_addons'] == 1)
    {
        $themeData['all_launcher_icons'] = \SocialKit\UI::view('story/publisher-box/launcher-icons/all', array(
            'key' => 'new_story_feature_launchericon',
            'return' => 'string',
            'type' => 'APPEND',
            'content' => array()
        ));
    }
    else
    {
        $themeData['all_launcher_icons'] = \SocialKit\UI::view('story/publisher-box/launcher-icons/all');
    }

    if ($continue) return \SocialKit\UI::view('story/publisher-box/content', 'story_publisher_ui_editor');
}