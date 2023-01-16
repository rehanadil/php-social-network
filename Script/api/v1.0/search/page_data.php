<?php
$pageQuery = $conn->query("SELECT address,awards,category_id,phone,products,website FROM " . DB_PAGES . " WHERE id=" . $profile['id']);
$page = $pageQuery->fetch_array(MYSQLI_ASSOC);
$profile_data = array_merge($profile, $page);

unset($profile_data['avatar_id']);
unset($profile_data['cover_id']);
unset($profile_data['category_id']);

/* Avatar Url */
$avatarQuery = $conn->query("SELECT extension,url FROM " . DB_MEDIA . " WHERE id=" . $profile['avatar_id'] . " AND active=1");

if ($avatarQuery->num_rows == 1)
{
	$avatar = $avatarQuery->fetch_array(MYSQLI_ASSOC);
	$profile_data['avatar_url'] = $site_url . '/' . $avatar['url'] . '_100x100.' . $avatar['extension'];
}

$api_data['results'][] = $profile_data;
