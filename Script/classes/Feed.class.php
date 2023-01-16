<?php

namespace SocialKit;

class Feed
{
	use \SocialTrait\Escape;

	private $conn;
	private $type = "all";
    private $id;
	private $afterId = 0;
	private $beforeId = 0;
	private $timelineId = 0;
    private $timelineObj;
    private $adminId = 0;
	private $limit = 5;
	private $startRow = 0;
	private $excludeActivity = false;
	private $timeline = array();
    private $data;
    private $pinned = false;
    private $boosted = false;
    private $saved = false;
    private $hidden = "0";

	function __construct()
    {
        global $conn;
        $this->conn = $conn;
        return $this;
    }

    public function setConnection(\mysqli $conn)
    {
        $this->conn = $conn;
        return $this;
    }

    protected function getConnection()
    {
        return $this->conn;
    }

	public function getFeed()
    {
		$query = $this->getStartQuery();
	    
	    if ($this->timelineId > 0)
        {
            $timelineQuery = $this->getTimelineQuery();

            if (empty($timelineQuery))
            {
                $this->data = array();
                return $this->data;
            }

	        $query .= $timelineQuery;

            if ($this->pinned)
            {
                $pinnedSql = "SELECT post_id FROM " . DB_PINNEDPOSTS . " WHERE active=1 AND timeline_id=" . $this->timelineId;
                $pinnedQuery = $this->getConnection()->query($pinnedSql);
                if ($pinnedQuery->num_rows === 1)
                {
                    $pinned = $pinnedQuery->fetch_array(MYSQLI_ASSOC);
                    $this->data[$pinned['post_id']] = $pinned['post_id'];
                }
            }
	    }
        elseif ($this->adminId > 0)
        {
            $query .= $this->getAdminQuery();
        }
        else
        {
	    	$query .= $this->getDefaultQuery();
	    }
	    
	    if ($this->startRow < 1)
        {
            $this->startRow = 0;
        }

	    if ($this->limit < 1)
        {
	        $this->limit = 5;
	    }
	    
	    $query .= $this->getEndQuery();
	    
	    if (!empty($query))
        {
            $sql_query = $this->getConnection()->query($query);
            
            if ($sql_query->num_rows == 0)
            {
                $this->data = array();
            }
            else
            {
                while ($sql_fetch = $sql_query->fetch_array(MYSQLI_ASSOC))
                {
                    $this->data[$sql_fetch['id']] = $sql_fetch['id'];
                }
            }
	    }
	    
	    return $this->data;
	}

	private function getStartQuery()
    {
		$query = "SELECT DISTINCT p.post_id AS id FROM " . DB_STORIES . " p";
	    return $query;
	}

    private function getEndQuery()
    {
        global $user;
        $partQuery = " AND p.id>0";

        if ($this->id > 0)
            $partQuery = " AND p.id=" . $this->id;
        elseif ($this->afterId > 0)
            $partQuery = " AND p.id<" . $this->afterId;
        elseif ($this->beforeId > 0)
            $partQuery = " AND p.id>" . $this->beforeId . " AND p.post_id<>" . $this->beforeId;

        $query = $partQuery;

        if ($this->boosted)
            $query .= " AND p.boosted=1";
        elseif ($this->saved)
            $query .= " AND p.id IN (SELECT post_id FROM " . DB_SAVEDPOSTS . " WHERE timeline_id=" . $user['id'] . ")";
        
        if ($this->startRow < 1) $this->startRow = 0;
        
        if ($this->limit < 1) $this->limit = 5;

        $query .= " AND p.active=1";

        if (isLogged()) $query .= " AND (p.timeline_id NOT IN (SELECT blocked_id FROM " . DB_BLOCKED_USERS . " WHERE blocker_id=" . $user['id'] . ") AND p.timeline_id NOT IN (SELECT blocker_id FROM " . DB_BLOCKED_USERS . " WHERE blocked_id=" . $user['id'] . "))";

        $query .= " AND p.id NOT IN (SELECT post_id FROM " . DB_PINNEDPOSTS . " WHERE timeline_id=" . $this->timelineId . ") ORDER BY p.id DESC LIMIT " . $this->startRow . "," . $this->limit;
        return $query;
    }

	private function getDefaultQuery()
    {
		if (! isLogged())
        {
            return false;
        }

        global $user;
        $query = '';

        switch ($this->type)
        {
            case 'texts':
                $query = " WHERE ((timeline_id IN
                            (SELECT id
                                FROM " . DB_ACCOUNTS . "
                                WHERE id IN
                                    (SELECT following_id
                                        FROM " . DB_FOLLOWERS . "
                                        WHERE follower_id=" . $user['id'] . "
                                        AND active=1
                                    )
                                AND active=1
                                AND banned=0
                            )
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN
                                (SELECT timeline_id
                                    FROM " . DB_STORIES . "
                                    WHERE boosted=1
                                )
                        )
                        OR (
                            recipient_id IN
                                (SELECT id
                                    FROM " . DB_ACCOUNTS . "
                                    WHERE id IN
                                        (SELECT following_id
                                            FROM " . DB_FOLLOWERS . "
                                            WHERE follower_id=" . $user['id'] . "
                                            AND following_id IN
                                                (SELECT id
                                                    FROM " . DB_GROUPS . ")
                                        )
                                    AND active=1
                                    AND banned=0
                                )
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")
                    AND google_map_name=''
                    AND media_id=0
                    AND soundcloud_uri=''
                    AND youtube_video_id=''";
            break;
            
            case 'photos':
                $query = " WHERE (
                        (timeline_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . "
                            WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1
                                )
                            AND active=1
                            AND banned=0
                            )
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN
                                (SELECT timeline_id FROM " . DB_STORIES . " WHERE boosted=1)
                        )
                        OR (recipient_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND following_id IN (SELECT id FROM " . DB_GROUPS . ")
                            )
                            AND active=1
                            AND banned=0)
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")
                    AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type IN ('photo','album'))";
            break;
            
            case 'videos':
                $query = " WHERE (
                        (timeline_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . "
                            WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1
                                )
                            AND active=1
                            AND banned=0
                            )
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN (SELECT timeline_id FROM " . DB_STORIES . " WHERE boosted=1)
                        )
                        OR (recipient_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND following_id IN (SELECT id FROM " . DB_GROUPS . ")
                            )
                            AND active=1
                            AND banned=0)
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")
                    AND (
                        (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='video') OR youtube_video_id<>'')
                        OR (post_id IN (SELECT story_id FROM story_video_uploads))
                    )";
            break;
            
            case 'music':
                $query = " WHERE (
                        (timeline_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . "
                            WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                            AND active=1
                            AND banned=0)
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN (SELECT timeline_id FROM " . DB_STORIES . " WHERE boosted=1)
                        )
                        OR (recipient_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND following_id IN (SELECT id FROM " . DB_GROUPS . ")
                            )
                            AND active=1
                            AND banned=0)
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")
                    AND (
                        (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='audio') OR soundcloud_uri<>'')
                        OR (post_id IN (SELECT story_id FROM story_music_uploads)) 
                    )";
            break;
            
            case 'places':
                $query = " WHERE (
                        (timeline_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . "
                            WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                            AND active=1
                            AND banned=0)
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN (SELECT timeline_id FROM " . DB_STORIES . " WHERE boosted=1)
                        )
                        OR (recipient_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND following_id IN (SELECT id FROM " . DB_GROUPS . ")
                            )
                            AND active=1
                            AND banned=0)
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")
                    AND google_map_name<>''";
            break;
            
            case 'likes':
                $query = " WHERE id IN (SELECT post_id FROM " . DB_POSTLIKES . ")
                    AND (
                        (timeline_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . "
                            WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                            AND active=1
                            AND banned=0)
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN (SELECT timeline_id FROM " . DB_STORIES . " WHERE boosted=1)
                        )
                        OR (recipient_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND following_id IN (SELECT id FROM " . DB_GROUPS . ")
                            )
                            AND active=1
                            AND banned=0)
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'shares':
                $query = " WHERE (
                        (timeline_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . "
                            WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                            AND active=1
                            AND banned=0)
                            OR timeline_id=" . $user['id'] . "
                            OR timeline_id IN (SELECT timeline_id FROM " . DB_STORIES . " WHERE boosted=1)
                        )
                        OR (recipient_id IN
                            (SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN
                                (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND following_id IN (SELECT id FROM " . DB_GROUPS . ")
                            )
                            AND active=1
                            AND banned=0)
                        )
                        OR boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")";
            break;
            
            default:
                $query = " WHERE (
                        (timeline_id IN
                            (SELECT id
                                FROM " . DB_ACCOUNTS . "
                                WHERE id IN
                                    (SELECT following_id
                                        FROM " . DB_FOLLOWERS . "
                                        WHERE follower_id=" . $user['id'] . "
                                        AND active=1
                                    )
                                AND active=1
                                AND banned=0
                            )
                            OR timeline_id=" . $user['id'] . "
                        )
                        OR
                        (recipient_id IN
                            (SELECT id
                                FROM " . DB_ACCOUNTS . "
                                WHERE id IN
                                    (SELECT following_id
                                        FROM " . DB_FOLLOWERS . "
                                        WHERE follower_id=" . $user['id'] . "
                                        AND following_id IN
                                            (SELECT id
                                                FROM " . DB_GROUPS . "
                                            )
                                    )
                                AND active=1
                                AND banned=0
                            )
                        )
                        OR
                        boosted=1
                    )
                    AND recipient_id NOT IN
                        (SELECT id FROM " . DB_GROUPS . "
                            WHERE group_privacy='secret'
                            AND id NOT IN
                            (SELECT following_id FROM " . DB_FOLLOWERS . "
                                WHERE follower_id=" . $user['id'] . "
                                AND active=1)
                        )
                    AND hidden IN (" . $this->hidden . ")";
        }
        
        return $query;
	}

	private function getTimelineQuery()
    {
		$query = '';
        $this->timelineObj = new \SocialKit\User();
        $this->timelineObj->setId($this->timelineId);
		$this->timeline = $this->timelineObj->getRows();

        if ($this->timeline['type'] == "user")
        {
        	$query = $this->getTimelineUserQuery();
        }
        elseif ($this->timeline['type'] == "page")
        {
            $query = $this->getTimelinePageQuery();
        }
        elseif ($this->timeline['type'] == "group")
        {
            $query = $this->getTimelineGroupQuery();
        }
        elseif ($this->timeline['type'] == "event")
        {
            $query = $this->getTimelineEventQuery();
        }

        return $query;
	}

	private function getTimelineUserQuery()
    {
        $query = '';

        switch ($this->type)
        {
            case 'texts':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id IN (0," . $this->timelineId . ")
                AND google_map_name=''
                AND media_id=0
                AND soundcloud_uri=''
                AND youtube_video_id=''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'photos':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id=0
                AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type IN ('photo','album'))
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'videos':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id=0
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='video') OR youtube_video_id<>'')
                    OR (post_id IN (SELECT story_id FROM story_video_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'music':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id=0
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='audio') OR soundcloud_uri<>'')
                    OR (post_id IN (SELECT story_id FROM story_music_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'places':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id=0
                AND google_map_name<>''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'likes':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id=0
                AND hidden IN (" . $this->hidden . ")
                AND id IN (SELECT post_id FROM " . DB_POSTLIKES . ")";
            break;
            
            case 'shares':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND recipient_id=0
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'timeline_post_by_others':
                $query = " WHERE recipient_id=" . $this->timelineId . " 
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            default:
                $query = " WHERE (p.timeline_id=" . $this->timelineId . " OR p.recipient_id=" . $this->timelineId . ")
                AND p.recipient_id NOT IN (SELECT id FROM " . DB_GROUPS . " WHERE group_privacy='secret')
                AND hidden IN (" . $this->hidden . ")";
        }

        return $query;
	}

	private function getTimelinePageQuery()
    {
		$query = '';

		switch ($this->type)
        {
            case 'texts':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND google_map_name=''
                AND media_id=0
                AND soundcloud_uri=''
                AND youtube_video_id=''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'photos':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type IN ('photo','album'))
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'videos':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='video') OR youtube_video_id<>'')
                    OR (post_id IN (SELECT story_id FROM story_video_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'music':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='audio') OR soundcloud_uri<>'')
                    OR (post_id IN (SELECT story_id FROM story_music_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'places':
                $query = " WHERE timeline_id=" . $this->timelineId . "
                AND google_map_name<>''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'timeline_post_by_others':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            default:
                $query = " WHERE (timeline_id=" . $this->timelineId . " OR recipient_id=" . $this->timelineId . ")
                AND hidden IN (" . $this->hidden . ")";
        }

        return $query;
	}

	private function getTimelineGroupQuery()
    {
		$query = '';

		switch ($this->type)
        {
            case 'texts':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND google_map_name=''
                AND media_id=0
                AND soundcloud_uri=''
                AND youtube_video_id=''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'photos':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type IN ('photo','album'))
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'videos':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='video') OR youtube_video_id<>'')
                    OR (post_id IN (SELECT story_id FROM story_video_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'music':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='audio') OR soundcloud_uri<>'')
                    OR (post_id IN (SELECT story_id FROM story_music_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'places':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND google_map_name<>''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            default:
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND hidden IN (" . $this->hidden . ")";
        }

        return $query;
	}

    private function getTimelineEventQuery()
    {
        $query = '';

        switch ($this->type)
        {
            case 'texts':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND google_map_name=''
                AND media_id=0
                AND soundcloud_uri=''
                AND youtube_video_id=''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'photos':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type IN ('photo','album'))
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'videos':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='video') OR youtube_video_id<>'')
                    OR (post_id IN (SELECT story_id FROM story_video_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'music':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='audio') OR soundcloud_uri<>'')
                    OR (post_id IN (SELECT story_id FROM story_music_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'places':
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND google_map_name<>''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            default:
                $query = " WHERE recipient_id=" . $this->timelineId . "
                AND hidden IN (" . $this->hidden . ")";
        }

        return $query;
    }

    private function getAdminQuery()
    {
        $query = '';
        switch ($this->type)
        {
            case 'texts':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND google_map_name=''
                AND media_id=0
                AND soundcloud_uri=''
                AND youtube_video_id=''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'photos':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type IN ('photo','album'))
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'videos':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='video') OR youtube_video_id<>'')
                    OR (post_id IN (SELECT story_id FROM story_video_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'music':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND (
                    (media_id IN (SELECT id FROM " . DB_MEDIA . " WHERE type='audio') OR soundcloud_uri<>'')
                    OR (post_id IN (SELECT story_id FROM story_music_uploads))
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'places':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND google_map_name<>''
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'likes':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND hidden IN (" . $this->hidden . ")
                AND id IN (SELECT post_id FROM " . DB_POSTLIKES . ")";
            break;
            
            case 'shares':
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            case 'timeline_post_by_others':
                $query = " WHERE recipient_id=" . $this->adminId . " 
                AND hidden IN (" . $this->hidden . ")";
            break;
            
            default:
                $query = " WHERE (
                    timeline_id=" . $this->adminId . "
                    OR timeline_id IN (SELECT page_id FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $this->adminId . ")
                )
                AND hidden IN (" . $this->hidden . ")";
        }

        return $query;
    }

    public function setId($i)
    {
        $this->id = (int) $i;
        if ($this->id > 0) $this->hidden = "0,1";
    }

    public function setHidden($b=true)
    {
        $this->hidden = ($b) ? "0,1" : "0";
    }

	public function setType($t) {
		$this->type = $this->postEscape($t, 'all');
	}

    public function setPinned($b)
    {
        if (is_bool($b)) $this->pinned = $b;
    }

    public function setBoosted($b)
    {
        if (is_bool($b)) $this->boosted = $b;
    }

    public function setSaved($b)
    {
        if (is_bool($b)) $this->saved = $b;
    }

	public function setAfterId($id) {
		$this->afterId = (int) $id;
	}

	public function setBeforeId($id) {
		$this->beforeId = (int) $id;
	}

	public function setTimelineId($id) {
		$this->timelineId = (int) $id;
	}

    public function setAdminId($id)
    {
        $this->adminId = (int) $id;
    }

	public function setLimit($l) {
		$this->limit = (int) $l;
	}
	
	public function setStart($l) {
		$this->startRow = (int) $l;
	}

	public function setExclude($b) {
		$this->excludeActivity = (bool) $b;
	}

    public function getTemplate()
    {
        if (!is_array($this->data)) $this->getFeed();

        global $themeData;
        $beforeData = $themeData;
        $storyList = '';

        foreach ($this->data as $storyId)
        {
            $storyObj = new \SocialKit\Story();
            $storyObj->setConnection($this->getConnection());
            $storyObj->setId($storyId);

            if ($this->timelineId > 0) $storyObj->setProfileId($this->timelineId);
            $storyList .= $storyObj->getTemplate();
        }

        if (empty($storyList)) $storyList = \SocialKit\UI::view('feed/none');

        $themeData['story_timeline_id'] = $this->timelineId;
        $themeData['story_list'] = $storyList;
        $themeData['boosted'] = $this->boosted;
        $themeData['saved'] = $this->saved;
        $return = \SocialKit\UI::view('feed/content');
        $themeData = $beforeData;
        return $return;
    }
}