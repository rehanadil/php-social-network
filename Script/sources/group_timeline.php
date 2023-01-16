<?php
$themeData['page_title'] = $timeline['name'];

$continue = true;

$isGroupAdmin = $timelineObj->isGroupAdmin();
$isFollowing = $timelineObj->isFollowing();
$isFollowedBy = $timelineObj->isFollowedBy();
$numFollowers = $timelineObj->numFollowers();
$numFollowRequests = $timelineObj->numFollowRequests();
$numGroupAdmins = $timelineObj->numGroupAdmins();
$numPosts = $timelineObj->numStories();
$joinButton = $timelineObj->getFollowButton();

if ($timeline['group_privacy'] === "secret")
{
    $continue = false;

    if ($isFollowedBy or $isGroupAdmin)
    {
        $continue = true;
    }
}

if (!$continue)
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=home');
    else
        header('Location: ' . smoothLink('index.php?a=home'));
}

if (!isset($_GET['b']))
{
    $_GET['b'] = "stories";
}

$themeData['timeline_num_members'] = $numFollowers;
$themeData['timeline_num_requests'] = $numFollowRequests;
$themeData['timeline_num_admins'] = $numGroupAdmins;
$themeData['timeline_num_posts'] = $numPosts;
$themeData['join_button'] = $joinButton;

/* */
$themeData['timeline_group_privacy'] = ucwords($timeline['group_privacy']);

$themeData['add_members_url'] = smoothLink('index.php?a=timeline&b=add_members&id=' . $timeline['username']);
$themeData['requests_url'] = smoothLink('index.php?a=timeline&b=requests&id=' . $timeline['username']);
$themeData['members_url'] = smoothLink('index.php?a=timeline&b=list_members&id='.$timeline['username']);
$themeData['admins_url'] = smoothLink('index.php?a=timeline&b=list_admins&id=' . $timeline['username']);
$themeData['posts_url'] = smoothLink('index.php?a=timeline&b=stories&id=' . $timeline['username']);
$themeData['settings_url'] = smoothLink('index.php?a=timeline&b=settings&id=' . $timeline['username']);
if (($timeline['add_privacy'] == "members" && $isFollowedBy)
    || ($timeline['add_privacy'] == "admins" && $isGroupAdmin))
{
    $themeData['add_group_members_html'] = \SocialKit\UI::view('timeline/group/add-members-link');
}

if ($isGroupAdmin && $numFollowRequests > 0)
{
    $themeData['group_requests_html'] = \SocialKit\UI::view('timeline/group/requests-link');
}

$themeData['group_members_html'] = \SocialKit\UI::view('timeline/group/members-link');
$themeData['group_admins_html'] = \SocialKit\UI::view('timeline/group/admins-link');
$themeData['group_posts_html'] = \SocialKit\UI::view('timeline/group/posts-link');

if ($isGroupAdmin)
{
    $themeData['timeline_buttons_html'] = \SocialKit\UI::view('timeline/group/timeline-buttons-admin');
}
elseif (isLogged())
{
    $themeData['timeline_buttons_html'] = \SocialKit\UI::view('timeline/group/timeline-buttons-default');
}

$themeData['timeline_info_about_html'] = \SocialKit\UI::view('timeline/group/timeline-info-about-html');
$themeData['timeline_info_html'] = \SocialKit\UI::view('timeline/group/timeline-info-html');
$themeData['no_one_to_add_label'] = '';

if ($config['friends'] == true)
{
    $themeData['no_one_to_add_label'] = $lang['no_friends_to_add_to_group'];
}
else
{
    $themeData['no_one_to_add_label'] = $lang['no_followers_to_add_to_group'];
}

$themeData['tab_content'] = null;

if ($_GET['b'] == "add_members")
{
    if (($timeline['add_privacy'] === "members" && $isFollowedBy) or ($timeline['add_privacy'] === "admins" && $isGroupAdmin))
    {
        $i = 0;
        $add_member_userlists = '';

        foreach ($userObj->getFollowers('', 0, true) as $followerId)
        {
            if (! $timelineObj->isFollowedBy($followerId))
            {
                $followerObj = new \SocialKit\User();
                $followerObj->setId($followerId);
                $follower = $followerObj->getRows();
                
                $themeData['add_member_user_id'] = $follower['id'];
                $themeData['add_member_user_url'] = $follower['url'];
                $themeData['add_member_user_username'] = $follower['username'];
                $themeData['add_member_user_thumbnail_url'] = $follower['thumbnail_url'];
                $themeData['add_member_user_name'] = $follower['name'];
                $themeData['add_member_user_info'] = "@".$follower['username'];
                
                $add_member_userlists .= \SocialKit\UI::view('timeline/group/add-members-userlist-each');
                $i++;
            }
        }

        if ($i == 0)
        {
            $add_member_userlists = \SocialKit\UI::view('timeline/group/add-members-nouser');
        }
        
        $themeData['add_member_userlists'] = $add_member_userlists;
        $themeData['tab_content'] = \SocialKit\UI::view('timeline/group/add-members-tab');
    }
}
elseif ($_GET['b'] == "list_members")
{
    $list_member_userlists = '';
    $foreach_indexes = array('list_member_user_id', 'list_member_user_url', 'list_member_username', 'list_member_user_thumbnail_url', 'list_member_user_name', 'list_members_make_admin_btn', 'list_members_btn');

    foreach ($timelineObj->getFollowers() as $memberId)
    {
        $memberObj = new \SocialKit\User();
        $memberObj->setId($memberId);
        $member = $memberObj->getRows();

        $themeData['list_member_user_id'] = $member['id'];
        $themeData['list_member_user_url'] = $member['url'];
        $themeData['list_member_user_username'] = $member['username'];
        $themeData['list_member_user_thumbnail_url'] = $member['thumbnail_url'];
        $themeData['list_member_user_name'] = $member['name'];
        $themeData['list_member_user_info'] = "@".$member['username'];

        if ($isGroupAdmin)
        {
            if (! $timelineObj->isGroupAdmin($member['id']))
            {
                $themeData['list_members_make_admin_btn'] = \SocialKit\UI::view('timeline/group/list-members-make-admin-btn');
            }

            $themeData['list_member_user_button'] = \SocialKit\UI::view('timeline/group/list-members-btn');
        }

        $list_member_userlists .= \SocialKit\UI::view('timeline/group/list-members-userlist-each');

        foreach($foreach_indexes as $fei => $fev)
        {
            $themeData[$fev] = null;
        }
    }

    if (empty($list_member_userlists)) $list_member_userlists = \SocialKit\UI::view('timeline/group/no-members');

    $themeData['list_member_userlists'] = $list_member_userlists;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/group/list-members-tab');
}
elseif ($_GET['b'] == "list_admins")
{
    $list_admin_userlists = '';
    $foreach_indexes = array('list_admin_user_id', 'list_admin_user_url', 'list_admin_username', 'list_admin_user_thumbnail_url', 'list_admin_user_name', 'list_admins_btn');
    
    $listAdminButtons = false;

    if ($isGroupAdmin && $timelineObj->numGroupAdmins() > 1)
    {
        $listAdminButtons = true;
    }

    foreach ($timelineObj->getGroupAdmins() as $adminId)
    {
        $adminObj = new \SocialKit\User();
        $adminObj->setId($adminId);
        $admin = $adminObj->getRows();

        $themeData['list_admin_user_id'] = $admin['id'];
        $themeData['list_admin_user_url'] = $admin['url'];
        $themeData['list_admin_user_username'] = $admin['username'];
        $themeData['list_admin_user_thumbnail_url'] = $admin['thumbnail_url'];
        $themeData['list_admin_user_name'] = $admin['name'];
        $themeData['list_admin_user_info'] = "@".$admin['username'];

        if ($listAdminButtons)
        {
            $themeData['list_admins_btn'] = \SocialKit\UI::view('timeline/group/list-admins-btn');
        }

        $list_admin_userlists .= \SocialKit\UI::view('timeline/group/list-admins-userlist-each');

        foreach($foreach_indexes as $fei => $fev) {
            $themeData[$fev] = null;
        }
    }

    if (empty($list_admin_userlists)) $list_admin_userlists = \SocialKit\UI::view('timeline/group/no-admins');

    $themeData['list_admin_userlists'] = $list_admin_userlists;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/group/list-admins-tab');
}
elseif ($_GET['b'] == "requests" && $isGroupAdmin)
{
    $group_request_userlists = '';

    foreach ($timelineObj->getFollowRequests() as $requestId)
    {
        $requestObj = new \SocialKit\User();
        $requestObj->setId($requestId);
        $request = $requestObj->getRows();

        $themeData['group_request_user_id'] = $request['id'];
        $themeData['group_request_user_url'] = $request['url'];
        $themeData['group_request_user_username'] = $request['username'];
        $themeData['group_request_user_thumbnail_url'] = $request['thumbnail_url'];
        $themeData['group_request_user_name'] = $request['name'];
        $themeData['group_request_user_info'] = "@".$request['username'];
        $themeData['group_requests_btn'] = \SocialKit\UI::view('timeline/group/group-requests-btn');

        $group_request_userlists .= \SocialKit\UI::view('timeline/group/group-requests-userlist-each');
    }

    $themeData['group_request_userlists'] = $group_request_userlists;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/group/group-requests-tab');
}
elseif ($_GET['b'] == "settings" && $isGroupAdmin)
{
    $checked_attr = '';
    if ($timeline['group_privacy'] == "open")
    {
        $checked_attr = ' checked';
    }
    $themeData['group_privacy_open_checked_attr'] = $checked_attr;


    $checked_attr = '';
    if ($timeline['group_privacy'] == "closed")
    {
        $checked_attr = ' checked';
    }
    $themeData['group_privacy_closed_checked_attr'] = $checked_attr;


    $checked_attr = '';
    if ($timeline['group_privacy'] == "secret")
    {
        $checked_attr = ' checked';
    }
    $themeData['group_privacy_secret_checked_attr'] = $checked_attr;


    $add_privacy_members_selected_attr = '';    
    if ($timeline['add_privacy'] == "members")
    {
        $add_privacy_members_selected_attr = ' selected';
    }
    $themeData['add_privacy_members_attr'] = $add_privacy_members_selected_attr;
    

    $add_privacy_admins_selected_attr = '';
    if ($timeline['add_privacy'] == "admins")
    {
        $add_privacy_admins_selected_attr = ' selected';
    }
    $themeData['add_privacy_admins_attr'] = $add_privacy_admins_selected_attr;


    $post_privacy_members_selected_attr = '';
    if ($timeline['timeline_post_privacy'] == "members")
    {
        $post_privacy_members_selected_attr = ' selected';
    }
    $themeData['post_privacy_members_attr'] = $post_privacy_members_selected_attr;
    

    $post_privacy_admins_selected_attr = '';
    if ($timeline['timeline_post_privacy'] == "admins")
    {
        $post_privacy_admins_selected_attr = ' selected';
    }
    $themeData['post_privacy_admins_attr'] = $post_privacy_admins_selected_attr;


    $themeData['username_html'] = \SocialKit\UI::view('timeline/group/settings/username');
    $themeData['name_html'] = \SocialKit\UI::view('timeline/group/settings/name');
    $themeData['about_html'] = \SocialKit\UI::view('timeline/group/settings/about');
    $themeData['group_privacy_html'] = \SocialKit\UI::view('timeline/group/settings/group-privacy');
    if ($config['group_allow_privacy_settings'] == 1) $themeData['add_privacy_html'] = \SocialKit\UI::view('timeline/group/settings/add-privacy');
    if ($config['group_allow_privacy_settings'] == 1) $themeData['post_privacy_html'] = \SocialKit\UI::view('timeline/group/settings/post-privacy');


    $themeData['tab_content'] = \SocialKit\UI::view('timeline/group/settings/content');
}
else
{
    $themeData['story_postbox'] = getStoryPostBox(0, $timeline['id']);

    $feedObj = new \SocialKit\Feed($conn);
    $feedObj->setTimelineId($timeline['id']);
    $feedObj->setPinned(true);
    $themeData['stories'] = $feedObj->getTemplate();
    $themeData['story_filters'] = storyFilters(array(
        "timeline_id" => $timeline['id']
    ));

    $themeData['tab_content'] = \SocialKit\UI::view('timeline/group/stories-tab');
}

$themeData['sidebar'] = \SocialKit\UI::view('timeline/group/sidebar');

if (isLogged() && $isGroupAdmin)
{
    $themeData['end'] = \SocialKit\UI::view('timeline/group/admin-end');
}
else
{
    $themeData['end'] = \SocialKit\UI::view('timeline/group/default-end');
}

/* */

$themeData['page_content'] = \SocialKit\UI::view('timeline/group/content');
