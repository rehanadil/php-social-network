<?php
$id = 0;

if (isLogged()) $id = $user['id'];

if (! empty($_GET['id']))
{
    if (is_numeric($_GET['id']))
    {
        $id = (int) $_GET['id'];
    }
    elseif (preg_match('/[A-Za-z0-9_]/', $id))
    {
        $escapeObj = new \SocialKit\Escape();
        $id = $escapeObj->stringEscape($_GET['id']);
    }
    
    $id = getUserId($conn, $id);
    
    if (! empty($id))
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($id);

        if (!$timelineObj->isBlocked())
        {
            $timeline = $timelineObj->getRows();
            
            if (is_array($timeline) && isset($timeline['id']))
            {
                $config['site_title'] = $timeline['name'];
                if (!empty($timeline['about'])) $themeData['site_description'] = substr($timeline['about'], 0, 157) . "...";

                foreach ($timeline as $key => $value)
                {
                    if (is_array($value))
                    {
                        foreach ($value as $key2 => $value2)
                        {
                            if (is_array($value))
                            {
                                $themeData['timeline_' . $key . '_' . $key2] = $value2;
                            }
                        }
                    }
                    else
                    {
                        $themeData['timeline_' . $key] = $value;
                    }
                }
                
                if ($timeline['type'] == "user")
                {
                    include('user_timeline.php');
                }
                elseif ($timeline['type'] == "page")
                {
                    include('page_timeline.php');
                }
                elseif ($timeline['type'] == "group")
                {
                    include('group_timeline.php');
                }
                elseif ($timeline['type'] == "event")
                {
                    include('event_timeline.php');
                }
            }
        }
    }
}
