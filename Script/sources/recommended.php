<?php
if (!isLogged())
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=logout');
	else
		header('Location: ' . smoothLink('index.php?a=logout'));
}
if (!isset($_GET['b'])
	|| !in_array($_GET['b'], array('users','pages','groups')))
{
	if ($ajax)
		$ajaxUrl = smoothLink('index.php?a=home');
	else
		header('Location: ' . smoothLink('index.php?a=home'));
}
$type = $_GET['b'];
$themeData['type'] = $type;
$themeData['page_title'] = $lang['recommended_' . $type];

switch ($type)
{
	case 'users':
		$query = $conn->query("SELECT id FROM " . DB_ACCOUNTS . "
		WHERE id IN
			(SELECT id FROM " . DB_USERS . "
			WHERE id NOT IN
				(SELECT following_id FROM " . DB_FOLLOWERS . "
				WHERE follower_id=" . $user['id'] . ")
			)
			AND (
				id NOT IN
					(SELECT blocked_id FROM " . DB_BLOCKED_USERS . " WHERE blocker_id=" . $user['id'] . ")
				AND id NOT IN
					(SELECT blocker_id FROM " . DB_BLOCKED_USERS . " WHERE blocked_id=" . $user['id'] . ")
			)
			AND id<>" . $user['id'] . "
			AND type='user'
			AND active=1
			AND banned=0
			ORDER BY RAND()
			LIMIT 50
		");
		break;
	
	case 'pages':
		$query = $conn->query("SELECT id FROM " . DB_ACCOUNTS . "
		WHERE id IN
			(SELECT id FROM " . DB_PAGES . "
			WHERE id NOT IN
				(SELECT following_id FROM " . DB_FOLLOWERS . "
				WHERE follower_id=" . $user['id'] . ")
			)
			AND (
				id NOT IN
					(SELECT blocked_id FROM " . DB_BLOCKED_USERS . " WHERE blocker_id=" . $user['id'] . ")
				AND id NOT IN
					(SELECT blocker_id FROM " . DB_BLOCKED_USERS . " WHERE blocked_id=" . $user['id'] . ")
			)
			AND type='page'
			AND active=1
			AND banned=0
			ORDER BY RAND()
			LIMIT 50
		");
		break;

	case 'groups':
		$query = $conn->query("SELECT id FROM " . DB_ACCOUNTS . "
		WHERE id IN
			(SELECT id FROM " . DB_GROUPS . "
			WHERE id NOT IN
				(SELECT following_id FROM " . DB_FOLLOWERS . "
				WHERE follower_id=" . $user['id'] . ")
			)
			AND (
				id NOT IN
					(SELECT blocked_id FROM " . DB_BLOCKED_USERS . " WHERE blocker_id=" . $user['id'] . ")
				AND id NOT IN
					(SELECT blocker_id FROM " . DB_BLOCKED_USERS . " WHERE blocked_id=" . $user['id'] . ")
			)
			AND type='group'
			AND active=1
			AND banned=0
			ORDER BY RAND()
			LIMIT 50
		");
		break;
}

$lis = "";
while ($rcm = $query->fetch_array(MYSQLI_ASSOC))
{
	$rcmObj = new \SocialKit\User();
	$rcmObj->setId($rcm['id']);
	$rcm = $rcmObj->getRows();

	$themeData['li_url'] = $rcm['url'];
	$themeData['li_id'] = $rcm['id'];
	$themeData['li_username'] = $rcm['username'];
	$themeData['li_name'] = $rcm['name'];
	$themeData['li_avatar'] = ($type === "groups") ? $rcm['cover_url'] : $rcm['avatar_url'];
	$themeData['li_btn'] = $rcmObj->getFollowButton();

	$lis .= \SocialKit\UI::view('recommended/li');
}

if (empty($lis))
{
	$lis .= \SocialKit\UI::view('recommended/none');
}

$themeData['li'] = $lis;
$themeData['page_content'] = \SocialKit\UI::view('recommended/content');
