<?php
userOnly();

$deactivate = $conn->query("UPDATE " . DB_ACCOUNTS . " SET active=0 WHERE id=" . $user['id']);

if ($deactivate)
{
	$cacheObj = new \SocialKit\Cache();
	$cacheObj->setType('user');
	$cacheObj->setId($user['id']);
	$cacheObj->prepare();

	if ($cacheObj->exists())
	{
		$cacheObj->clear();
	}

	$data = array(
        'status' => 200,
        'url' => smoothLink('index.php?a=home')
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();