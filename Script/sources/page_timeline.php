<?php
$themeData['page_title'] = $timeline['name'];
$isPageAdmin = $timelineObj->isAdmin();

if (isset($_GET['b']) && $_GET['b'] == "settings" && $isPageAdmin)
{
    if (empty($_GET['c'])) $_GET['c'] = 'general_settings';

    $themeData['general_settings_url'] = smoothLink('index.php?a=timeline&b=settings&c=general_settings&id=' . $timeline['username']);
	$themeData['general_link'] = \SocialKit\UI::view('timeline/page/admin/sidebar/general-link');
    
    $themeData['privacy_link'] = "";
    if ($config['page_allow_privacy_settings'] == 1)
    {
        $themeData['privacy_settings_url'] = smoothLink('index.php?a=timeline&b=settings&c=privacy&id=' . $timeline['username']);
        $themeData['privacy_link'] = \SocialKit\UI::view('timeline/page/admin/sidebar/privacy-link');
    }

    if ($isPageAdmin === "admin")
    {
        $themeData['admins_roles_url'] = smoothLink('index.php?a=timeline&b=settings&c=admins&id=' . $timeline['username']);
        $themeData['admin_roles_link'] = \SocialKit\UI::view('timeline/page/admin/sidebar/admin-roles-link');
    }

    $themeData['messages_settings_url'] = smoothLink('index.php?a=timeline&b=settings&c=messages&id=' . $timeline['username']);
    $themeData['messages_count'] = $timelineObj->numMessages();
    $themeData['messages_link'] = \SocialKit\UI::view('timeline/page/admin/sidebar/messages-link');

	$themeData['likes_settings_url'] = smoothLink('index.php?a=timeline&b=settings&c=likes&id=' . $timeline['username']);
    $themeData['likes_link'] = \SocialKit\UI::view('timeline/page/admin/sidebar/likes-link');

    $themeData['sidebar'] = \SocialKit\UI::view('timeline/page/admin/sidebar/content');

    if (isset($_GET['c']) && $_GET['c'] == "general_settings")
    {
        $value_attr = '';

        if (!empty($timeline['phone']))
        {
            $value_attr = ' value="' . $timeline['phone'] . '"';
        }

        $themeData['phone_value_attr'] = $value_attr;
        $themeData['tab_content'] = \SocialKit\UI::view('timeline/page/admin/general-settings-tab');
    }
    elseif (isset($_GET['c']) && $_GET['c'] == "privacy" && $config['page_allow_privacy_settings'] == 1)
    {
        $post_privacy_everyone_selected_attr = '';
        
        if ($timeline['timeline_post_privacy'] == "everyone")
        {
            $post_privacy_everyone_selected_attr = ' selected';
        }

        $themeData['post_privacy_everyone_selected_attr'] = $post_privacy_everyone_selected_attr;
        $post_privacy_none_selected_attr = '';

        if ($timeline['timeline_post_privacy'] == "none")
        {
            $post_privacy_none_selected_attr = ' selected';
        }

        $themeData['post_privacy_none_selected_attr'] = $post_privacy_none_selected_attr;
        $message_privacy_everyone_selected_attr = '';

        if ($timeline['message_privacy'] == "everyone")
        {
            $message_privacy_everyone_selected_attr = ' selected';
        }

        $themeData['message_privacy_everyone_selected_attr'] = $message_privacy_everyone_selected_attr;
        $message_privacy_none_selected_attr = '';

        if ($timeline['message_privacy'] == "none")
        {
            $message_privacy_none_selected_attr = ' selected';
        }

        $themeData['message_privacy_none_selected_attr'] = $message_privacy_none_selected_attr;
        $themeData['tab_content'] = \SocialKit\UI::view('timeline/page/admin/privacy-settings-tab');
    }
    elseif (isset($_GET['c']) && $_GET['c'] == "admins" && $isPageAdmin == "admin")
    {
        $admins_i = 0;
        $themeData['admins_list'] = '';

        foreach ($timelineObj->getPageAdmins() as $adminId)
        {
            $adminObj = new \SocialKit\User();
            $adminObj->setId($adminId);
            $admin = $adminObj->getRows();

            $themeData['list_admin_id'] = $admin['id'];
            $themeData['list_admin_url'] = $admin['url'];
            $themeData['list_admin_username'] = $admin['username'];
            $themeData['list_admin_name'] = $admin['name'];
            $themeData['list_admin_thumbnail_url'] = $admin['thumbnail_url'];

            $admin_role = $timelineObj->isPageAdmin($admin['id']);
            $themeData['list_admin_role'] = ucfirst($admin_role);

            if ($admin_role === "admin")
            {
                $themeData['list_admin_selected_attr'] = 'selected';
            }
            elseif ($admin_role === "editor")
            {
                $themeData['list_editor_selected_attr'] = 'selected';

            }

            $themeData['admins_list'] .= \SocialKit\UI::view('timeline/page/admin/list-admins-each');
            $admins_i++;
        }

        if ($admins_i == 0)
        {
            $themeData['admins_list'] .= \SocialKit\UI::view('timeline/page/admin/no-admins');
        }

        $nonadmin_followings = 0;
        $themeData['potential_admins_list'] = '';

        foreach ($userObj->getFollowings() as $potentialId)
        {
            if (! $timelineObj->isPageAdmin($potentialId))
            {
                $potentialObj = new \SocialKit\User();
                $potentialObj->setId($potentialId);
                $potential = $potentialObj->getRows();

                $themeData['list_pa_id'] = $potential['id'];
                $themeData['list_pa_url'] = $potential['url'];
                $themeData['list_pa_username'] = $potential['username'];
                $themeData['list_pa_name'] = $potential['name'];
                $themeData['list_pa_thumbnail_url'] = $potential['thumbnail_url'];
                $themeData['list_pa_info'] = "@" . $potential['username'];

                $themeData['potential_admins_list'] .= \SocialKit\UI::view('timeline/page/admin/list-potential-admins-each');
                $nonadmin_followings++;
            }
        }
        
        if ($nonadmin_followings == 0)
        {
            if ($config['friends'] == true)
            {
                $themeData['no_admin_to_add_label'] = $lang['no_friends_to_add_to_page'];
            }
            else
            {
                $themeData['no_admin_to_add_label'] = $lang['no_followers_to_add_to_page'];
            }

            $themeData['potential_admins_list'] .= \SocialKit\UI::view('timeline/page/admin/no-potential-admins');
        }

        $themeData['tab_content'] = \SocialKit\UI::view('timeline/page/admin/admins-tab');
    }
    elseif (isset($_GET['c']) && $_GET['c'] == "messages" && $isPageAdmin)
    {
        if (! empty($_GET['recipient_id']))
        {
            if (! is_numeric($_GET['recipient_id']))
            {
                $_GET['recipient_id'] = getUserId($conn, $_GET['recipient_id']);
            }

            $recipientId = (int) $_GET['recipient_id'];
            $recipientObj = new \SocialKit\User();
            $recipientObj->setId($recipientId);
            $recipient = $recipientObj->getRows();
            
            $hidden_class = '';

            if (! isset($recipient['id']))
            {
                $hidden_class = ' hidden';
            }
            $themeData['recipient_name_class'] = $hidden_class;

            $themeData['recipient_id'] = $recipient['id'];
            $themeData['recipient_name'] = $recipient['name'];

            $no_messages = true;
            $listMessages = '';
            
            $messages = getMessagesHtml(
                array(
                    'recipient_id' => $recipient['id'],
                    'timeline_id' => $timeline['id']
                )
            );
            
            if (countMessages(
                array(
                    'recipient_id' => $recipient['id'],
                    'timeline_id' => $timeline['id']
                )
            ) < 1) {
                $messages .= \SocialKit\UI::view('timeline/page/admin/no-messages');
            }

            $themeData['list_messages'] = $messages;
            $themeData['tab_content'] = \SocialKit\UI::view('timeline/page/admin/messages-conversation-tab');

        } else {
            $themeData['tab_content'] = \SocialKit\UI::view('timeline/page/admin/messages-default-tab');

        }

    }
    elseif (isset($_GET['c']) && $_GET['c'] == "likes" && $isPageAdmin)
    {
        $likesList = '';

        if ($timelineObj->numFollowers() == 0)
        {
            $likesList .= \SocialKit\UI::view('timeline/page/admin/no-likes');
        }
        else
        {
            foreach ($timelineObj->getFollowers() as $fanId)
            {
                $fanObj = new \SocialKit\User();
                $fanObj->setId($fanId);
                $fan = $fanObj->getRows();

                $themeData['list_like_user_id'] = $fan['id'];
                $themeData['list_like_user_url'] = $fan['url'];
                $themeData['list_like_user_username'] = $fan['username'];
                $themeData['list_like_user_thumbnail_url'] = $fan['thumbnail_url'];
                $themeData['list_like_user_name'] = $fan['name'];
                $themeData['list_like_user_info'] = "@".$fan['username'];

                $likesList .= \SocialKit\UI::view('timeline/page/admin/likes-each');

            }
        }

        $themeData['likes_list'] = $likesList;
        $themeData['tab_content'] = \SocialKit\UI::view('timeline/page/admin/likes-tab');
    }

    $themeData['page_content'] = \SocialKit\UI::view('timeline/page/admin/content');
}
else
{
    $themeData['likes_num'] = $timelineObj->numFollowers();
    $themeData['posts_num'] = $timelineObj->numStories();
    $themeData['like_button'] = $timelineObj->getFollowButton();
    $themeData['posts_tab_url'] = smoothLink('index.php?a=timeline&b=stories&id=' . $timeline['username']);
    $themeData['settings_tab_url'] = smoothLink('index.php?a=timeline&b=settings&id=' . $timeline['username']);
    $themeData['messages_tab_url'] = smoothLink('index.php?a=messages&recipient_id=' . $timeline['username']);

    if ($isPageAdmin)
    {
        $themeData['change_avatar_html'] = \SocialKit\UI::view('timeline/page/change-avatar');
        $themeData['timeline_buttons'] = \SocialKit\UI::view('timeline/page/timeline-buttons-admin');
    }
    elseif (isLogged())
    {
        if ($timeline['message_privacy'] == "everyone")
        {
            $themeData['message_button'] = \SocialKit\UI::view('timeline/page/message-button');
        }

        $themeData['timeline_buttons'] = \SocialKit\UI::view('timeline/page/timeline-buttons-default');
    }

    if ($timeline['verified'] == true)
    {
        $themeData['verified_badge'] = \SocialKit\UI::view('timeline/page/verified-badge');
    }

    $category = getPageCategoryData($timeline['category_id']);
    $themeData['category_name'] = $category['name'];

    if (!empty($timeline['about']))
    {
        $themeData['info_about_row'] = \SocialKit\UI::view('timeline/page/info-about-row');
    }

    if (!empty($timeline['address']))
    {
        $themeData['info_address_row'] = \SocialKit\UI::view('timeline/page/info-address-row');
    }

    if (!empty($timeline['awards']))
    {
        $themeData['info_awards_row'] = \SocialKit\UI::view('timeline/page/info-awards-row');
    }

    if (!empty($timeline['phone']))
    {
        $themeData['info_phone_row'] = \SocialKit\UI::view('timeline/page/info-phone-row');
    }

    if (!empty($timeline['products']))
    {
        $themeData['info_products_row'] = \SocialKit\UI::view('timeline/page/info-products-row');
    }

    $themeData['sidebar_albums'] = $timelineObj->getAlbumsTemplate();
    $themeData['sidebar_photos'] = $timelineObj->getPhotosTemplate();

    $themeData['sidebar'] = \SocialKit\UI::view('timeline/page/sidebar');

    if (isLogged())
    {
        if ($isPageAdmin)
        {
            $themeData['story_postbox'] = getStoryPostBox($timeline['id']);
        }
        else
        {
            $themeData['story_postbox'] = getStoryPostBox(0, $timeline['id']);

        }
    }

    $feedObj = new \SocialKit\Feed($conn);
    $feedObj->setTimelineId($timeline['id']);
    $feedObj->setPinned(true);
    $themeData['stories'] = $feedObj->getTemplate();

    $themeData['story_filters'] = storyFilters(array(
        "timeline_id" => $timeline['id']
    ));

    if ($sk['logged'] == true && $isPageAdmin) {
        $themeData['end'] = \SocialKit\UI::view('timeline/page/admin-end');
    } else {
        $themeData['end'] = \SocialKit\UI::view('timeline/page/default-end');
    }

    $themeData['page_content'] = \SocialKit\UI::view('timeline/page/content');
}