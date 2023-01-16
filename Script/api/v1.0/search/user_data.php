<?php
$userQuery = $conn->query("SELECT birthday,current_city,gender,hometown FROM " . DB_USERS . " WHERE id=" . $profile['id']);
$user = $userQuery->fetch_array(MYSQLI_ASSOC);
$profile_data = array_merge($profile, $user);

unset($profile_data['avatar_id']);
unset($profile_data['cover_id']);

/* Avatar Url */
$avatarQuery = $conn->query("SELECT extension,url FROM " . DB_MEDIA . " WHERE id=" . $profile['avatar_id'] . " AND active=1");

if ($avatarQuery->num_rows == 1)
{
	$avatar = $avatarQuery->fetch_array(MYSQLI_ASSOC);
	$profile_data['avatar_url'] = $site_url . '/' . $avatar['url'] . '_100x100.' . $avatar['extension'];
}

$api_data['results'][] = $profile_data;