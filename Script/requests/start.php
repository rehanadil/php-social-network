<?php
userOnly();

if (isset($_POST['follow'])
	&& is_array($_POST['follow']))
{
	$follows = $_POST['follow'];
	foreach ($follows as $fid)
	{
		$fid = (int) $fid;
		$fiObj = new \SocialKit\User();
		$fiObj->setId($fid);
		$fiObj->getRows();
		$fiObj->putFollow();
	}

	$conn->query("UPDATE " . DB_USERS . " SET start_up=1 WHERE id=" . $user['id']);
}

if (isset($_GET['done']))
{
	$conn->query("UPDATE " . DB_USERS . " SET start_up=1 WHERE id=" . $user['id']);
}

$data = array(
	"status" => 200,
	"url" => smoothLink('index.php?a=home')
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();