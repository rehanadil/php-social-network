<?php
if (isset($_POST['key'], $_POST['value']))
{
	$continue = false;

	if ($_POST['key'] === "deactivate")
	{
		$deactivate = $conn->query("UPDATE " . DB_ACCOUNTS . " SET active=0 WHERE id=" . $user['id']);
		
		if ($deactivate)
		{
			$continue = true;

			$cacheObj = new \SocialKit\Cache();
	        $cacheObj->setType('user');
	        $cacheObj->setId($user['id']);
	        $cacheObj->prepare();

	        if ($cacheObj->exists())
	        {
	            $cacheObj->clear();
	        }
		}
	}
	else
	{
		if (updateTimeline(array(
			$_POST['key'] => $_POST['value']
		)))
		{
			$continue = true;
		}
	}

	if ($continue)
	{
		$data = array(
	        'status' => 200
	    );
	}
}