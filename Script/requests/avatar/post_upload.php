<?php
$continue = false;
$processed = false;

if ($_POST['timeline_id'] == $user['id'])
{
    $timelineId = $user['id'];
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
            $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET avatar_id=" . $avatar['id'] . " WHERE id=$timelineId AND banned=0");
            
            if ($query)
            {
                $cacheObj = new \SocialKit\Cache();
                $cacheObj->setType('user');
                $cacheObj->setId($timeline['id']);
                $cacheObj->prepare();

                if ($cacheObj->exists())
                {
                    $cacheObj->clear();
                }
                
                $processed = true;
            }
        }
    }

    if (!empty($_POST['redirect_url']))
    {
        $redirect_url = $escapeObj->stringEscape($_POST['redirect_url']);
        header('Location: ' . $redirect_url);
    }
    else
    {
        if ($processed == true)
        {
            header('Location: ' . smoothLink('index.php?a=timeline&id=' . $timeline['username']));
        }
        else
        {
            header('Location: ' . smoothLink('index.php?a=settings&b=avatar'));
        }
    }
}
else
{
    header('Location: ' . subscriptionUrl());
}