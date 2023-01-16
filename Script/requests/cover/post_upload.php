<?php
if ($user['subscription_plan']['update_cover'] == 1)
{
    if (isset($_FILES['image']['tmp_name']))
    {
        $coverImage = $_FILES['image'];
        $continue = false;
        $processed = false;
        
        $timelineId = (int) $_POST['timeline_id'];
        $timelineObj = new \SocialKit\User($conn);
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();
        
        if (isset($timeline['id']))
        {
            if ($timelineObj->isAdmin())
            {
                $continue = true;
            }
        }
        
        if ($continue == true)
        {
            $coverData = registerCoverImage($coverImage);
            
            if (isset($coverData['id']))
            {
                $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET cover_id=" . $coverData['id'] . ",cover_position=0 WHERE id=$timelineId AND banned=0");
                
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
        
        if (! empty($_POST['redirect_url']))
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
}
else
{
    header('Location: ' . subscriptionUrl());
}