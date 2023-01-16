<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}
if ($user['start_up'] != 0)
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=home');
	else
		header('Location: ' . smoothLink('index.php?a=home'));
}
$themeData['page_title'] = $lang['start_page_title'];

// Birthday
if (in_array($user['birthday'], array('1-1-1990', '01/01/1990')))
{
	$themeData['birthday_input'] = \SocialKit\UI::view('start/birthday');
}

// Countries
$themeData['countries'] = json_encode(getCountries());

// Follow Button
$themeData['follow_all'] = ($config['friends'] == 1) ? $lang['start_add_friend_all'] : $lang['start_follow_all'];
$themeData['follow_none'] = ($config['friends'] == 1) ? $lang['start_add_friend_none'] : $lang['start_follow_none'];
$themeData['follow_num'] = ($config['friends'] == 1) ? $lang['start_add_friend_num'] : $lang['start_follow_num'];
$themeData['follow_people'] = ($config['friends'] == 1) ? $lang['add_some_people'] : $lang['follow_some_people'];

// Follow List
$followQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " AS u
	WHERE id IN (SELECT id FROM " . DB_USERS . " WHERE follow_privacy='everyone')
	AND id NOT IN (SELECT following_id FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $user['id'] . ")
	AND id<>" . $user['id'] . "
	AND type='user'
	AND avatar_id>0
	AND active=1
	AND banned=0
	ORDER BY (SELECT COUNT(id) FROM " . DB_STORIES . " WHERE timeline_id=u.id) DESC
	LIMIT 100");
$liHtml = "";
while ($fo = $followQuery->fetch_array(MYSQLI_ASSOC))
{
	$foObj = new \SocialKit\User();
	$foObj->setId($fo['id']);
	$fo = $foObj->getRows();

	$themeData['li_id'] = $fo['id'];
	$themeData['li_username'] = $fo['username'];
	$themeData['li_name'] = $fo['name'];
	$themeData['li_avatar'] = $fo['avatar_url'];
	$themeData['li_thumbnail'] = $fo['thumbnail_url'];

	$liHtml .= \SocialKit\UI::view('start/follow-li');
}
$themeData['follow_li'] = $liHtml;
$themeData['page_content'] = \SocialKit\UI::view('start/content');