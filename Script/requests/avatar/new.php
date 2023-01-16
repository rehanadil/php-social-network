<?php
$continue = false;

if ($_POST['timeline_id'] == $user['id'])
{
    $timeline = $user;
    $timelineObj = $userObj;
    $continue = true;
}
else
{
    $timelineId = (int) $_POST['timeline_id'];
    $timelineObj = new \SocialKit\User($conn);
    $timelineObj->setId($timelineId);
    $timeline = $timelineObj->getRows();
}

if ($continue == false && isset($timeline['id']))
{
    if ($timelineObj->isAdmin())
    {
        $continue = true;
    }
}

if ($user['subscription_plan']['update_avatar'] == 1)
{
    if (isset($_FILES['image']['tmp_name']) && $continue == true)
    {
        $image = $_FILES['image'];
        $avatar = registerMedia($image);
        
        if (isset($avatar['id']))
        {
            $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=" . $timeline['id'] . " AND banned=0");
            
            if ($query)
            {
                unset($_SESSION['tempche']['user'][$timeline['id']]);
                $data = array(
                    'status' => 200,
                    'avatar_url' => $config['site_url'] . '/' . $avatar['url'] . '_100x100.' . $avatar['extension']
                );

                $cacheObj = new \SocialKit\Cache();
                $cacheObj->setType('user');
                $cacheObj->setId($timeline['id']);
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

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();