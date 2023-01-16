<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['settings_label'];
$themeData['nav_page'] = 'settings';

/* */
$themeData['general_settings_url'] = smoothLink('index.php?a=settings&b=general');
if ($config['user_allow_privacy_settings'] == 1) $themeData['privacy_settings_url'] = smoothLink('index.php?a=settings&b=privacy');
$themeData['email_notification_settings_url'] = smoothLink('index.php?a=settings&b=email_notifications');
$themeData['avatar_settings_url'] = smoothLink('index.php?a=settings&b=avatar');
$themeData['cover_settings_url'] = smoothLink('index.php?a=settings&b=cover');
$themeData['password_settings_url'] = smoothLink('index.php?a=settings&b=password');
$themeData['blocked_users_url'] = smoothLink('index.php?a=settings&b=blocked-users');
$themeData['deactivate_url'] = smoothLink('index.php?a=settings&b=deactivate');

if (! isset($_GET['b']) or $_GET['b'] == "general")
{
    $themeData['countries'] = json_encode(getCountries());

	$birthDateOptions = '';

	for ($i=1; $i<32; $i++)
    {
        if (strlen($i) === 1) $i = "0" . $i;

        if ($i == $user['birth']['date'])
            $option = '<option value="' . $i . '" selected>' . $i . '</option>';
        else
            $option = '<option value="' . $i . '">' . $i . '</option>';
        
        $birthDateOptions .= $option;
    }

    $themeData['birth_date_options'] = $birthDateOptions;
    $birthMonthOptions = '';

    foreach (getMonths() as $month_number => $month_data)
    {
        if (strlen($month_number) === 1) $month_number = "0" . $month_number;
        
        if ($month_number == $user['birth']['month'])
            $option = '<option value="' . $month_number . '" selected>' . $month_data[1] . '</option>';
        else
            $option = '<option value="' . $month_number . '">' . $month_data[1] . '</option>';
        
        $birthMonthOptions .= $option;
    }

    $themeData['birth_month_options'] = $birthMonthOptions;
    $birthYearOptions = '';

    for ($i = date('Y')-100; $i < date('Y')-12; $i++)
    {
        if ($i == $user['birth']['year'])
        {
            $option = '<option value="' . $i . '" selected>' . $i . '</option>';
        }
        else
        {
            $option = '<option value="' . $i . '">' . $i . '</option>';
        }
        
    	$birthYearOptions .= $option;
    }

    $themeData['birth_year_options'] = $birthYearOptions;

    // Gender
    if ($user['gender'] == "male")
    {
        $themeData['gender_male_selected_attr'] = 'selected';
    } elseif ($user['gender'] == "female")
    {
        $themeData['gender_female_selected_attr'] = 'selected';
    }

    $timezoneOptions = '';
    foreach (getTimezones() as $tz)
    {
        if ($tz == $user['timezone'])
            $option = '<option value="' . $tz . '" selected>' . $tz . '</option>';
        else
            $option = '<option value="' . $tz . '">' . $tz . '</option>';

        $timezoneOptions .= $option;
    }

    $themeData['timezone_options'] = $timezoneOptions;

    $themeData['user_about'] = str_replace("<br>", "\n", $themeData['user_about']);

    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/general-settings-tab');
}
elseif (isset($_GET['b']) && $_GET['b'] == "privacy" && $config['user_allow_privacy_settings'] == 1)
{

	if ($config['friends'] == true) {
        $themeData['people_i_follow_label'] = $lang['my_friends'];
        
    } else {
        $themeData['people_i_follow_label'] = $lang['people_i_follow'];
    }

	if ($config['friends'] != true) {

		if ($user['confirm_followers'] == 0) {
            $themeData['follow_request_no_selected_attr'] = 'selected';

        } elseif ($user['confirm_followers'] == 1) {
            $themeData['follow_request_yes_selected_attr'] = 'selected';
        }

        if ($user['follow_privacy'] == "everyone") {
            $themeData['follow_privacy_everyone_selected_attr'] = 'selected';

        } elseif ($user['follow_privacy'] == "following") {
            $themeData['follow_privacy_following_selected_attr'] = 'selected';
        }

        $themeData['follow_privacy_rows'] = \SocialKit\UI::view('timeline/user/settings/follow-privacy-rows');
	}

	if ($user['message_privacy'] == "everyone") {
        $themeData['message_privacy_everyone_selected_attr'] = 'selected';

    } elseif ($user['message_privacy'] == "following") {
        $themeData['message_privacy_following_selected_attr'] = 'selected';
    }

    if ($user['comment_privacy'] == "everyone") {
        $themeData['comment_privacy_everyone_selected_attr'] = 'selected';

    } elseif ($user['comment_privacy'] == "following") {
        $themeData['comment_privacy_following_selected_attr'] = 'selected';
    }

    if ($user['timeline_post_privacy'] == "everyone") {
        $themeData['timeline_post_privacy_everyone_selected_attr'] = 'selected';

    } elseif ($user['timeline_post_privacy'] == "following") {
        $themeData['timeline_post_privacy_following_selected_attr'] = 'selected';

    } elseif ($user['timeline_post_privacy'] == "none") {
        $themeData['timeline_post_privacy_none_selected_attr'] = 'selected';
    }

    if ($user['post_privacy'] == "everyone") {
        $themeData['post_privacy_everyone_selected_attr'] = 'selected';
    }
    
    if ($user['post_privacy'] == "following") {
        $themeData['post_privacy_following_selected_attr'] = 'selected';
    }

    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/privacy-settings-tab');

}
elseif (isset($_GET['b']) && $_GET['b'] == "email_notifications")
{
    if ($user['mailnotif_follow'] == true)
    {
        $themeData['follow_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_friendrequests'] == true)
    {
        $themeData['friendrequest_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_comment'] == true)
    {
        $themeData['comment_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_postlike'] == true)
    {
        $themeData['postlike_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_postshare'] == true)
    {
        $themeData['postshare_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_groupjoined'] == true)
    {
        $themeData['groupjoined_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_pagelike'] == true)
    {
        $themeData['pagelike_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_message'] == true)
    {
        $themeData['message_email_notification_checked'] = 'checked';
    }

    if ($user['mailnotif_timelinepost'] == true)
    {
        $themeData['timelinepost_email_notification_checked'] = 'checked';
    }

    if ($config['friends'] == true)
    {
        $themeData['friendrequest_email_row'] = \SocialKit\UI::view('timeline/user/settings/email-notifications/friendrequest-row');
    }
    else
    {
        $themeData['follow_email_row'] = \SocialKit\UI::view('timeline/user/settings/email-notifications/follow-row');
    }

    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/email-notifications-settings-tab');
}
elseif (isset($_GET['b']) && $_GET['b'] == "avatar") {
	$themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/avatar-settings-tab');

} elseif (isset($_GET['b']) && $_GET['b'] == "cover") {
	$themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/cover-settings-tab');

} elseif (isset($_GET['b']) && $_GET['b'] == "password") {
	$themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/password-settings-tab');

} elseif (isset($_GET['b']) && $_GET['b'] == "deactivate") {
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/deactivate-tab');

}
elseif (isset($_GET['b']) && $_GET['b'] == "blocked-users")
{
    $blockedUsersHtml = "";
    $blockedUsersSql = "SELECT blocked_id FROM " . DB_BLOCKED_USERS . " WHERE active=1 AND blocker_id=" . $user['id'];
    $blockedUsersQuery = $conn->query($blockedUsersSql);
    
    if ($blockedUsersQuery->num_rows > 0)
    {
        while ($blockedUserInfo = $blockedUsersQuery->fetch_array(MYSQLI_ASSOC)) {
            $blockedUserObj = new \SocialKit\User();
            $blockedUserObj->setId($blockedUserInfo['blocked_id']);
            $blockedUser = $blockedUserObj->getRows();
            
            $themeData['blocked_user_id'] = $blockedUser['id'];
            $themeData['blocked_user_url'] = $blockedUser['url'];
            $themeData['blocked_user_username'] = $blockedUser['username'];
            $themeData['blocked_user_name'] = $blockedUser['name'];
            $themeData['blocked_user_thumbnail_url'] = $blockedUser['thumbnail_url'];
            $themeData['blocked_user_info'] = '@'.$blockedUser['username'];

            $blockedUsersHtml .= \SocialKit\UI::view('timeline/user/settings/blocked-user-column');
        }
    }
    else
    {
        $blockedUsersHtml = \SocialKit\UI::view('timeline/user/settings/blocked-user-none');
    }

    $themeData['blocked_users'] = $blockedUsersHtml;
    $themeData['tab_content'] = \SocialKit\UI::view('timeline/user/settings/blocked-users-tab');
}

/* Links */
$themeData['general_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/general-link');
if ($config['user_allow_privacy_settings'] == 1) $themeData['privacy_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/privacy-link');
$themeData['email_notification_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/email-notification-link');
$themeData['avatar_change_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/avatar-change-link');
$themeData['cover_change_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/cover-change-link');
$themeData['password_change_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/password-change-link');
$themeData['blocked_users_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/blocked-users-link');
$themeData['deactivate_settings_link'] = \SocialKit\UI::view('timeline/user/settings/sidebar/deactivate-link');

$themeData['sidebar'] = \SocialKit\UI::view('timeline/user/settings/sidebar/content');

$themeData['page_content'] = \SocialKit\UI::view('timeline/user/settings/content');
