<?php
$_POST['pos'] = $escapeObj->stringEscape($_POST['pos']);
$reposition = false;
$position = preg_replace('/[^0-9]/', '', $_POST['pos']);
$width = 920;

if (isset($_POST['width']))
{
	$width = (int) $_POST['width'];
}

$timelineId = (int) $_POST['timeline_id'];
$timelineObj = new \SocialKit\User($conn);
$timelineObj->setId($timelineId);
$timeline = $timelineObj->getRows();

if (isset($timeline['id']))
{
    $cover_id = $timeline['cover']['id'];
    
    if ($timelineObj->isAdmin())
    {
        $reposition = true;
    }
}

if ($user['subscription_plan']['update_cover'] == 1)
{
    if ($reposition == true)
    {
        $cover_url = createCover($cover_id, ($position / $width));
        
        if ($cover_url)
        {
            $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET cover_position=$position WHERE id=$timelineId AND active=1 AND banned=0");
            
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
                
                $data = array(
                    'status' => 200,
                    'url' => $config['site_url'] . '/' . $cover_url
                );
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