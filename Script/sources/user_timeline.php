<?php
$changeAvatar = (isLogged() && $timeline['id'] === $user['id']) ? true : false;
if ($changeAvatar) $themeData['change_avatar_html'] = \SocialKit\UI::view('timeline/user/change-avatar');
if ($timeline['verified'] == true) $themeData['verified_badge'] = \SocialKit\UI::view('timeline/user/verified-badge');

$themeData['page_title'] = $timeline['name'];

$owner = false;
$post_visibility_privacy = true;
$themeData['followrequests_num'] = $timelineObj->numFollowRequests();
$themeData['following_num'] = $timelineObj->numFollowing();
$themeData['followers_num'] = $timelineObj->numFollowers();
$themeData['page_likes_num'] = $timelineObj->numPageLikes();
$themeData['groups_joined_num'] = $timelineObj->numGroupsJoined();
$themeData['stories_num'] = $timelineObj->numStories();

$themeData['followrequests_tab_url'] = smoothLink('index.php?a=timeline&b=requests&id=' . $timeline['username']);
$themeData['friends_tab_url'] = smoothLink('index.php?a=timeline&b=friends&id=' . $timeline['username']);
$themeData['following_tab_url'] = smoothLink('index.php?a=timeline&b=following&id=' . $timeline['username']);
$themeData['followers_tab_url'] = smoothLink('index.php?a=timeline&b=followers&id=' . $timeline['username']);
$themeData['page_likes_tab_url'] = smoothLink('index.php?a=timeline&b=likes&id=' . $timeline['username']);
$themeData['groups_joined_tab_url'] = smoothLink('index.php?a=timeline&b=groups&id=' . $timeline['username']);
$themeData['stories_tab_url'] = smoothLink('index.php?a=timeline&b=stories&id=' . $timeline['username']);
$themeData['settings_tab_url'] = smoothLink('index.php?a=settings');

if (isset($user))
{
    if ($timeline['id'] == $user['id'])
    {
        $owner = true;
    }
}

if ($timeline['post_privacy'] == "following")
{
    $post_visibility_privacy = false;

    if (isset($user) && is_array($user))
    {
        if ($owner == true or $timelineObj->isFollowing())
        {
            $post_visibility_privacy = true;
        }
    }
}

if (isLogged() && $owner == true && $themeData['followrequests_num'] > 0)
{
	$themeData['follow_requests_link'] = \SocialKit\UI::view('timeline/user/follow-requests-link');
}

if ($post_visibility_privacy == true)
{
	$themeData['stories_link'] = \SocialKit\UI::view('timeline/user/stories-link');
}

if ($config['friends'] == true)
{
	$themeData['following_link'] = \SocialKit\UI::view('timeline/user/friends-link');
}
else
{
	$themeData['following_link'] = \SocialKit\UI::view('timeline/user/following-link');
}


if ($timeline['subscription_plan']['is_default'] == 0)
{
    $themeData['timeline_plan_view'] = \SocialKit\UI::view('timeline/user/plan-view');
}


if (isLogged() == true)
{
    if ($owner == true)
    {
        $themeData['timeline_buttons'] = \SocialKit\UI::view('timeline/user/timeline-buttons-admin');
    }
    else
    {
        $showFollowButton = false;

        if ($config['friends'] == true)
        {
            $showFollowButton = true;
        }
        else
        {
            if ($timeline['follow_privacy'] == "everyone" or ($timeline['follow_privacy'] == "following" && $timelineObj->isFollowing()))
            {
                $showFollowButton = true;
            }
        }

        if ($showFollowButton == true)
        {
            $themeData['follow_button'] = $timelineObj->getFollowButton();
            $themeData['follow_button_html'] = \SocialKit\UI::view('timeline/user/follow-button');
        }

        if ($timeline['message_privacy'] == "everyone" or ($timeline['message_privacy'] == "following" && $timelineObj->isFollowing()))
        {
            $themeData['message_button_url'] = smoothLink('index.php?a=messages&recipient_id=' . $timeline['id']);
            $themeData['message_button_html'] = \SocialKit\UI::view('timeline/user/message-button');
        }

        $themeData['timeline_buttons'] = \SocialKit\UI::view('timeline/user/timeline-buttons-default');
    }
}

$themeData['gender_label'] = ucfirst($lang['gender_' . $timeline['gender'] . '_label']);

if (! empty($timeline['gender']))
{
    $themeData['info_gender_row'] = \SocialKit\UI::view('timeline/user/info-gender-row');
}

if (! empty($timeline['birth']))
{
    $months = getMonths();
    $birthMonth = ucfirst($months[$timeline['birth']['month']][1]);

    if ($timeline['birthday_visibility'] === "month_date_and_year")
    {
        $themeData['timeline_birthday'] = $birthMonth . " " . $timeline['birth']['date'] . ", " . $timeline['birth']['year'];
        $themeData['info_birthday_row'] = \SocialKit\UI::view('timeline/user/info-birthday-row');
    }
    elseif ($timeline['birthday_visibility'] === "month_and_date")
    {
        $themeData['timeline_birthday'] = $birthMonth . " " . $timeline['birth']['date'];
        $themeData['info_birthday_row'] = \SocialKit\UI::view('timeline/user/info-birthday-row');
    }
}

if (! empty($timeline['current_city']))
{
    $themeData['info_location_row'] = \SocialKit\UI::view('timeline/user/info-location-row');
}

if (! empty($timeline['hometown']))
{
    $themeData['info_hometown_row'] = \SocialKit\UI::view('timeline/user/info-hometown-row');
}

if (! empty($timeline['about']))
{
    $themeData['info_about_row'] = \SocialKit\UI::view('timeline/user/info-about-row');
}

if (isset($_GET['b']) && $_GET['b'] == "requests" && $owner == true)
{
    if ($config['friends'] == true)
    {
        $themeData['follow_requests_label'] = $lang['friends_requests_label'];
        $themeData['no_follow_requests_label'] = $timeline['name'] . ' ' . $lang['no_friends'];
    }
    else
    {
        $themeData['follow_requests_label'] = $lang['follow_requests_label'];
        $themeData['no_follow_requests_label'] = $timeline['name'] . ' ' . $lang['no_follow_requests'];
    }

    $i = 0;
    $listFollowRequests = '';

    foreach ($timelineObj->getFollowRequests() as $requestId)
    {
        $requestObj = new \SocialKit\User();
        $requestObj->setId($requestId);
        $request = $requestObj->getRows();

        $themeData['list_request_id'] = $request['id'];
        $themeData['list_request_url'] = $request['url'];
        $themeData['list_request_username'] = $request['username'];
        $themeData['list_request_name'] = $request['name'];
        $themeData['list_request_thumbnail_url'] = $request['thumbnail_url'];
        $themeData['list_request_info'] = '@'.$request['username'];

        if ($owner == true) $themeData['list_request_button'] = \SocialKit\UI::view('timeline/user/follow-request-button');

        $listFollowRequests .= \SocialKit\UI::view('timeline/user/list-followrequests-each');
        $i++;
    }

    if ($i < 1)
    {
        $listFollowRequests .= \SocialKit\UI::view('timeline/user/no-requests');
    }

    $themeData['list_follow_requests'] = $listFollowRequests;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/requests-tab');
}
elseif (isset($_GET['b']) && ($_GET['b'] == "following" or $_GET['b'] == "friends"))
{
    if ($config['friends'] == true)
    {
        $themeData['following_header_label'] = $lang['friends_label'];
        $themeData['no_followings'] = $lang['no_friends'];
    }
    else
    {
        $themeData['following_header_label'] = $lang['following_label'];
        $themeData['no_followings'] = $lang['no_followings'];
    }

    $listFollowings = '';
    $i = 0;
    
    foreach ($timelineObj->getFollowings() as $followingId)
    {
        $followingObj = new \SocialKit\User();
        $followingObj->setId($followingId);
        $following = $followingObj->getRows();

        $themeData['list_following_id'] = $following['id'];
        $themeData['list_following_url'] = $following['url'];
        $themeData['list_following_username'] = $following['username'];
        $themeData['list_following_name'] = $following['name'];
        $themeData['list_following_thumbnail_url'] = $following['thumbnail_url'];
        $themeData['list_following_info'] = '@'.$following['username'];

        $themeData['list_following_button'] = $followingObj->getFollowButton();

        $listFollowings .= \SocialKit\UI::view('timeline/user/list-followings-each');
        $i++;
    }

    if ($i < 1)
    {
        $listFollowings .= \SocialKit\UI::view('timeline/user/no-followings');
    }

    $themeData['list_followings'] = $listFollowings;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/followings-tab');
}
elseif (isset($_GET['b']) && $_GET['b'] == "followers" && $config['friends'] !== true)
{
    $listFollowers = '';
    $i = 0;

    foreach ($timelineObj->getFollowers() as $followerId)
    {
        $followerObj = new \SocialKit\User();
        $followerObj->setId($followerId);
        $follower = $followerObj->getRows();

        $themeData['list_follower_id'] = $follower['id'];
        $themeData['list_follower_url'] = $follower['url'];
        $themeData['list_follower_username'] = $follower['username'];
        $themeData['list_follower_name'] = $follower['name'];
        $themeData['list_follower_thumbnail_url'] = $follower['thumbnail_url'];
        $themeData['list_follower_info'] = '@'.$follower['username'];

        $themeData['list_follower_button'] = $followerObj->getFollowButton();

        $listFollowers .= \SocialKit\UI::view('timeline/user/list-followers-each');
        $i++;
    }

    if ($i < 1)
    {
        $listFollowers .= \SocialKit\UI::view('timeline/user/no-followers');
    }

    $themeData['list_followers'] = $listFollowers;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/followers-tab');
}
elseif (isset($_GET['b']) && $_GET['b'] == "likes")
{
    $listPageLikes = '';
    $i = 0;
    
    foreach ($timelineObj->getLikedPages() as $pageId)
    {
        $pageObj = new \SocialKit\User();
        $pageObj->setId($pageId);
        $page = $pageObj->getRows();

        $themeData['list_page_id'] = $page['id'];
        $themeData['list_page_url'] = $page['url'];
        $themeData['list_page_username'] = $page['username'];
        $themeData['list_page_name'] = $page['name'];
        $themeData['list_page_thumbnail_url'] = $page['thumbnail_url'];

        $category = getPageCategoryData($page['category_id']);
        $themeData['list_page_info'] = $category['name'];

        $themeData['list_page_button'] = $pageObj->getFollowButton();

        $listPageLikes .= \SocialKit\UI::view('timeline/user/list-page-likes-each');
        $i++;
    }

    if ($i < 1)
    {
        $listPageLikes .= \SocialKit\UI::view('timeline/user/no-page-likes');
    }

    $themeData['list_page_likes'] = $listPageLikes;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/page-likes-tab');
}
elseif (isset($_GET['b']) && $_GET['b'] == "groups")
{
    $listGroupsJoined = '';
    $i = 0;

    foreach ($timelineObj->getGroupsJoined() as $groupId)
    {
        $groupObj = new \SocialKit\User();
        $groupObj->setId($groupId);
        $group = $groupObj->getRows();

        $themeData['list_group_id'] = $group['id'];
        $themeData['list_group_url'] = $group['url'];
        $themeData['list_group_username'] = $group['username'];
        $themeData['list_group_name'] = $group['name'];
        $themeData['list_group_thumbnail_url'] = $group['thumbnail_url'];
        $themeData['list_group_info'] = $lang[strtolower(($group['group_privacy'])) . '_group'];

        $themeData['list_group_button'] = $groupObj->getFollowButton();

        $listGroupsJoined .= \SocialKit\UI::view('timeline/user/list-groups-joined-each');
        $i++;
    }

    if ($i < 1)
    {
        $listGroupsJoined .= \SocialKit\UI::view('timeline/user/no-groups-joined');
    }

    $themeData['list_groups_joined'] = $listGroupsJoined;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/groups-joined-tab');
}
else
{
    if (isLogged() == true)
    {
        $themeData['story_postbox'] = getStoryPostBox(0, $timeline['id']);
    }

    $themeData['story_filters'] = storyFilters(array(
        "timeline_id" => $timeline['id']
    ));

    $feedObj = new \SocialKit\Feed($conn);
    $feedObj->setTimelineId($timeline['id']);
    $feedObj->setPinned(true);
    $themeData['stories'] = $feedObj->getTemplate();
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/stories-tab');
}

if ($post_visibility_privacy == true)
{
    $themeData['sidebar_albums'] = $timelineObj->getAlbumsTemplate();
    $themeData['sidebar_photos'] = $timelineObj->getPhotosTemplate();
}

if ($owner == true)
{
    $themeData['end'] = \SocialKit\UI::view('timeline/user/admin-end');
}
else
{
    $themeData['end'] = \SocialKit\UI::view('timeline/user/default-end');
}

/* */
$themeData['page_content'] = \SocialKit\UI::view('timeline/user/content');