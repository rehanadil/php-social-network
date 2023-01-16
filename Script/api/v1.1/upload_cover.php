<?php
if ($continue)
{
	if ($user['subscription_plan']['update_cover'] == 1)
	{
	    if (isset($_FILES['image']['tmp_name']))
	    {
	        $image = $_FILES['image'];
	        $cover = registerCoverImage($image);
	        
	        if (isset($cover['id']))
            {
                $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET cover_id=" . $cover['id'] . ",cover_position=0 WHERE id=" . $user['id']);
                
                if ($query)
                {
                    $data = array(
                        'status' => 200,
                        'cover' => $config['site_url'] . '/' . $cover['cover_url']
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