<?php
/* Update Timeline */
function updateTimeline($data=array())
{
    if (!isLogged()) return false;
    
    global $conn;
    $timelineId = 0;

    if (isset($data['timeline_id'])) $timelineId = (int) $data['timeline_id'];

    if ($timelineId < 1)
    {
        global $user, $userObj;
        $timelineId = $user['id'];
        $timelineObj = $userObj;
        $timeline = $user;
    }
    else
    {
        $timelineObj = new \SocialKit\User();
        $timelineObj->setId($timelineId);
        $timeline = $timelineObj->getRows();

        if (!isset($timeline['id'])) return false;
    }
    
    if (!$timelineObj->isAdmin()) return false;

    $cacheObj = new \SocialKit\Cache();
    $cacheObj->setType('user');
    $cacheObj->setId($timeline['id']);
    $cacheObj->prepare();

    if ($cacheObj->exists())
    {
        $cacheObj->clear();
    }

    $update_query_one = "UPDATE " . DB_ACCOUNTS . " SET ";
    $escapeObj = new \SocialKit\Escape();
    
    if (! empty($data['name']))
    {
        $name = $escapeObj->stringEscape($data['name']);
        $update_query_one .= "name='$name',";
    }
    
    if (! empty($data['username']))
    {
        $username = $escapeObj->stringEscape($data['username']);

        if (validateUsername($username) && getUsernameStatus($username, $timelineId) == 200)
        {
            $update_query_one .= "username='$username',";
        }
        elseif (getUsernameStatus($username, $timelineId) == 201)
        {
            // do nothing!
        }
    }
    
    if (! empty($data['about']))
    {
        $about = $escapeObj->postEscape($data['about']);
        $update_query_one .= "about='$about',";
    }
    
    if ($timeline['type'] == "user")
    {
        if (! empty($data['email']))
        {
            $email = $escapeObj->stringEscape($data['email']);
            $update_query_one .= "email='$email',";
        }
        
        if (isset($data['timezone']))
        {
            $timezone = $escapeObj->stringEscape($data['timezone']);
            $timezones = getTimezones();
            
            if (in_array($timezone, $timezones))
            {
                $update_query_one .= "timezone='$timezone',";
            }
        }
        
        if (! empty($data['new_password']) && ! empty($data['current_password']))
        {
            $updatePass = false;
            $passwordQuery = $conn->query("SELECT password
                FROM " . DB_ACCOUNTS . "
                WHERE id=" . $timelineId . "
                AND banned=0");
            
            if ($passwordQuery->num_rows == 1)
            {
                $passwordFetch = $passwordQuery->fetch_array(MYSQLI_ASSOC);
                
                if (md5(trim($data['current_password'])) == $passwordFetch['password'])
                {
                    $updatePass = true;
                }
            }
            
            if ($updatePass)
            {
                $newPassword = md5(trim($escapeObj->stringEscape($data['new_password'])));
                $update_query_one .= "password='$newPassword',";
            }
        }
    }
    
    if (isset($data['avatar']['name']))
    {
        $avatar = $data['avatar'];
        $avatarData = registerMedia($avatar);
        
        if (isset($avatarData['id']))
        {
            $update_query_one .= "avatar_id=" . $avatarData['id'] . ",";
        }
    }
    
    if (isset($data['cover']['name']))
    {
        $cover = $data['cover'];
        $coverData = registerMedia($cover);
        
        if (isset($coverData['id']))
        {
            $update_query_one .= "cover_id=" . $coverData['id'] . ",";
        }
    }
    
    $update_query_one .= "active=1 WHERE banned=0 AND id=" . $timelineId;
    $sql_query_one = $conn->query($update_query_one);
    
    
    if ($timeline['type'] == "user")
    {
        $update_query_two = "UPDATE " . DB_USERS . " SET ";
        
        if (isset($data['birthday']))
        {
            if (is_array($data['birthday']))
            {
                $continue = true;
                if (count($data['birthday']) !== 3) $continue = false;
                if ($data['birthday'][0] < 1 || $data['birthday'][0] > 12) $continue = false;
                if ($data['birthday'][1] < 1 || $data['birthday'][0] > 31) $continue = false;
                if ($data['birthday'][2] < 1900 || $data['birthday'][0] > date('Y')) $continue = false;
                if ($data['birthday'][1] === 2 && $data['birthday'][0] > 29) $continue = false;

                if (strlen($data['birthday'][0]) == 1) $data['birthday'][0] = "0" . $data['birthday'][0];
                if (strlen($data['birthday'][1]) == 1) $data['birthday'][1] = "0" . $data['birthday'][1];

                if ($continue)
                {
                    $birthday = $escapeObj->stringEscape(implode('/', $data['birthday']));
                    $update_query_two .= "birthday='$birthday',";
                }
            }
            elseif (preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,4}$/i', $data['birthday']))
            {
                $continue = true;
                $bdayExplode = explode('/', $data['birthday']);

                if (count($bdayExplode) !== 3) $continue = false;
                if ($bdayExplode[0] < 1 || $bdayExplode[0] > 12) $continue = false;
                if ($bdayExplode[1] < 1 || $bdayExplode[0] > 31) $continue = false;
                if ($bdayExplode[2] < 1900 || $bdayExplode[0] > date('Y')) $continue = false;
                if ($bdayExplode[1] === 2 && $bdayExplode[0] > 29) $continue = false;

                if (strlen($bdayExplode[0]) == 1) $bdayExplode[0] = "0" . $bdayExplode[0];
                if (strlen($bdayExplode[1]) == 1) $bdayExplode[1] = "0" . $bdayExplode[1];

                if ($continue)
                {
                    $birthday = $escapeObj->stringEscape(implode('/', $bdayExplode));
                    $update_query_two .= "birthday='$birthday',";
                }
            }
        }
        
        if (! empty($data['gender']))
        {
            $data['gender'] = $escapeObj->stringEscape($data['gender']);
            
            if (preg_match('/(male|female)/', $data['gender']))
            {
                $update_query_two .= "gender='" . $data['gender'] . "',";
            }
        }

        if (isset($data['country']))
        {
            $country = (int) $data['country'];
            if (getCountries($country))
            {
                $update_query_two .= "country_id=$country,";
            }
        }
        
        if (isset($data['current_city']))
        {
            $location = $escapeObj->stringEscape($data['current_city']);
            $update_query_two .= "current_city='$location',";
        }

        if (isset($data['location']))
        {
            $location = $escapeObj->stringEscape($data['location']);
            $update_query_two .= "current_city='$location',";
        }
        
        if (isset($data['hometown']))
        {
            $hometown = $escapeObj->stringEscape($data['hometown']);
            $update_query_two .= "hometown='$hometown',";
        }

        if (isset($data['birthday_visibility'])
            && in_array($data['birthday_visibility'], array('month_date_and_year','month_and_date','none')))
        {
            $birthdayVisibility = $data['birthday_visibility'];
            $update_query_two .= "birthday_visibility='$birthdayVisibility',";
        }
        
        if (isset($data['confirm_followers']) && preg_match('/(0|1)/', $data['confirm_followers']))
        {
            $confirmFollowers = (int) $data['confirm_followers'];
            $update_query_two .= "confirm_followers=$confirmFollowers,";
        }
        
        if (isset($data['follow_privacy']) && preg_match('/(everyone|following)/', $data['follow_privacy']))
        {
            $followPrivacy = $data['follow_privacy'];
            $update_query_two .= "follow_privacy='$followPrivacy',";
        }
        
        if (isset($data['comment_privacy']) && preg_match('/(everyone|following)/', $data['comment_privacy']))
        {
            $commentPrivacy = $data['comment_privacy'];
            $update_query_two .= "comment_privacy='$commentPrivacy',";
        }
        
        if (isset($data['message_privacy']) && preg_match('/(everyone|following)/', $data['message_privacy']))
        {
            $messagePrivacy = $data['message_privacy'];
            $update_query_two .= "message_privacy='$messagePrivacy',";
        }
        
        if (isset($data['timeline_post_privacy']) && preg_match('/(everyone|following|none)/', $data['timeline_post_privacy']))
        {
            $timelinePostPrivacy = $data['timeline_post_privacy'];
            $update_query_two .= "timeline_post_privacy='$timelinePostPrivacy',";
        }

        if (isset($data['post_privacy']) && preg_match('/(everyone|following)/', $data['post_privacy'])) {
            $postPrivacy = $data['post_privacy'];
            $update_query_two .= "post_privacy='$postPrivacy',";
        }

        if (isset($data['mailnotif_follow']))
        {
            $mailnotifFollow = (in_array($data['mailnotif_follow'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_follow=$mailnotifFollow,";
        }

        if (isset($data['mailnotif_friendrequests']))
        {
            $mailnotifFriendrequests = (in_array($data['mailnotif_friendrequests'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_friendrequests=$mailnotifFriendrequests,";
        }

        if (isset($data['mailnotif_comment']))
        {
            $mailnotifComment = (in_array($data['mailnotif_comment'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_comment=$mailnotifComment,";
        }

        if (isset($data['mailnotif_postlike']))
        {
            $mailnotifPostlike = (in_array($data['mailnotif_postlike'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_postlike=$mailnotifPostlike,";
        }

        if (isset($data['mailnotif_postshare']))
        {
            $mailnotifPostShare = (in_array($data['mailnotif_postshare'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_postshare=$mailnotifPostShare,";
        }

        if (isset($data['mailnotif_groupjoined']))
        {
            $mailnotifGroupJoined = (in_array($data['mailnotif_groupjoined'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_groupjoined=$mailnotifGroupJoined,";
        }

        if (isset($data['mailnotif_pagelike']))
        {
            $mailnotifPagelike = (in_array($data['mailnotif_pagelike'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_pagelike=$mailnotifPagelike,";
        }

        if (isset($data['mailnotif_message']))
        {
            $mailnotifMessage = (in_array($data['mailnotif_message'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_message=$mailnotifMessage,";
        }

        if (isset($data['mailnotif_timelinepost']))
        {
            $mailnotifTimelinepost = (in_array($data['mailnotif_timelinepost'], array("yes", "1", 1))) ? 1 : 0;
            $update_query_two .= "mailnotif_timelinepost=$mailnotifTimelinepost,";
        }
        
        $update_query_two .= "id=id WHERE id=" . $timeline['id'];
        $sql_query_two = $conn->query($update_query_two);
    }
    elseif ($timeline['type'] == "page")
    {
        $update_query_two = "UPDATE " . DB_PAGES . " SET ";
        
        if (isset($data['timeline_post_privacy']) && preg_match('/(everyone|none)/', $data['timeline_post_privacy']))
        {
            $timelinePostPrivacy = $data['timeline_post_privacy'];
            $update_query_two .= "timeline_post_privacy='$timelinePostPrivacy',";
        }
        
        if (isset($data['message_privacy']) && preg_match('/(everyone|none)/', $data['message_privacy']))
        {
            $messagePrivacy = $data['message_privacy'];
            $update_query_two .= "message_privacy='$messagePrivacy',";
        }
        
        if (! empty($data['address']))
        {
            $address = $escapeObj->stringEscape($data['address']);
            $update_query_two .= "address='$address',";
        }
        
        if (! empty($data['awards']))
        {
            $awards = $escapeObj->stringEscape($data['awards']);
            $update_query_two .= "awards='$awards',";
        }
        
        if (! empty($data['phone']))
        {
            $phone = $escapeObj->stringEscape($data['phone']);
            $update_query_two .= "phone='$phone',";
        }
        
        if (! empty($data['products']))
        {
            $products = $escapeObj->stringEscape($data['products']);
            $update_query_two .= "products='$products',";
        }
        
        if (! empty($data['website']))
        {
            $website = $escapeObj->stringEscape($data['website']);
            $update_query_two .= "website='$website',";
        }
        
        $update_query_two .= "id=id WHERE id=" . $timeline['id'];
        $sql_query_two = $conn->query($update_query_two);
    }
    elseif ($timeline['type'] == "group")
    {
        $update_query_two = "UPDATE ". DB_GROUPS ." SET ";
        
        if (! empty($data['group_privacy']) && preg_match('/(open|closed|secret)/', $data['group_privacy']))
        {
            $groupPrivacy = $escapeObj->stringEscape($data['group_privacy']);
            $update_query_two .= "group_privacy='$groupPrivacy',";
        }
        
        if (!empty($data['add_privacy']) && preg_match('/(members|admins)/', $data['add_privacy']))
        {
            $addPrivacy = $escapeObj->stringEscape($data['add_privacy']);
            $update_query_two .= "add_privacy='$addPrivacy',";
        }
        
        if (! empty($data['timeline_post_privacy']))
        {
            $timelinePostPrivacy = $escapeObj->stringEscape($data['timeline_post_privacy']);
            $update_query_two .= "timeline_post_privacy='$timelinePostPrivacy',";
        }
        
        $update_query_two .= "id=id WHERE id=" . $timeline['id'];
        $sql_query_two = $conn->query($update_query_two);
    }

    if ($cacheObj->exists())
    {
        $cacheObj->clear();
    }
    return true;
}
