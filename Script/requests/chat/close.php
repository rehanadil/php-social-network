<?php
if (isset($_GET['rid']))
{
	$rid = (int) $_GET['rid'];
	unset($_SESSION['chat_friends'][$rid]);
}
$conn->close();
exit();