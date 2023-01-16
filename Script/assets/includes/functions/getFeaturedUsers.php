<?php
function getFeaturedUsers()
{
	if (!isLogged()) return false;
	global $conn, $themeData;
	$reset = $themeData;
	$query = $conn->query("SELECT id FROM " . DB_ACCOUNTS . "
		WHERE id IN
			(SELECT id FROM " . DB_USERS . "
			WHERE subscription_plan IN
				(SELECT id FROM " . DB_SUBSCRIPTION_PLANS . " WHERE featured=1)
			)
		AND active=1
		AND banned=0
		ORDER BY RAND()
		LIMIT 6");
	$lis = "";
	while ($fu = $query->fetch_array(MYSQLI_ASSOC))
	{
		$fuObj = new \SocialKit\User();
		$fuObj->setId($fu['id']);
		$fu = $fuObj->getRows();

		$themeData['li_url'] = $fu['url'];
		$themeData['li_username'] = $fu['username'];
		$themeData['li_name'] = $fu['name'];
		$themeData['li_avatar'] = $fu['avatar_url'];
		$lis .= \SocialKit\UI::view('featured-users/li');
	}
	$themeData = $reset;
	return $lis;
}