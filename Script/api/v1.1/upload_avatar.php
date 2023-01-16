<?php
if ($continue)
{
	if ($user['subscription_plan']['update_avatar'] == 1)
	{
	    if (isset($_FILES['image']['tmp_name']))
	    {
	        $image = $_FILES['image'];
	        $avatar = registerMedia($image);
	        
	        if (isset($avatar['id']))
	        {
	            $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=" . $user['id']);
	            
	            if ($query)
	            {
	                unset($_SESSION['tempche']['user'][$user['id']]);
	                $data = array(
	                    'status' => 200,
	                    'image' => $config['site_url'] . '/' . $avatar['url'] . '_100x100.' . $avatar['extension']
	                );

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
	    }
	}
	else
	{
	    $data['url'] = subscriptionUrl();
	}
}