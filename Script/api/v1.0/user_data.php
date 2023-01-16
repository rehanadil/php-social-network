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

$api_data['api_status'] = "success";
$api_data['profile_data'] = $profile_data;

unset($api_data['errors']);
header("Content-type: application/json; charset=utf-8");
echo json_encode($api_data);
$conn->close();
exit();