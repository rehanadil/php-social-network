<?php
if ($config['page_allow_create'] == 1 && $user['subscription_plan']['create_pages'] == 1)
{
	$registerArray = array(
	    'name' => $_POST['page_name'],
	    'username' => $_POST['page_username'],
	    'about' => $_POST['page_about'],
	    'category_id' => $_POST['page_category_id']
	);

	$regObj = new \SocialKit\registerPage();
	$regObj->setName($_POST['page_name']);
	$regObj->setUsername($_POST['page_username']);
	$regObj->setAbout($_POST['page_about']);
	$regObj->setCatId($_POST['page_category_id']);

	if ($register = $regObj->register())
	{
	    $data = array(
	        'status' => 200,
	        'url' => $register['url']
	    );
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();