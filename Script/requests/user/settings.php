<?php
userOnly();
if (!isset($_POST['username'])) $_POST['username'] = $user['username'];

if (updateTimeline($_POST))
{
    $data = array(
        'status' => 200,
        'url' => smoothLink('index.php?a=timeline&id=' . $_POST['username'])
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();