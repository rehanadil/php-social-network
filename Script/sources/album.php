<?php
if ($_GET['b'] === "create")
{
    require_once('sources/create_album.php');
}
else
{
    $albumId = (int) $_GET['b'];
    $queryOne = $conn->query("SELECT id,name,descr,timeline_id FROM " . DB_MEDIA . " WHERE id=$albumId AND type='album' AND temp=0 AND active=1");

    if ($queryOne->num_rows == 1)
    {
        $album = $queryOne->fetch_array(MYSQLI_ASSOC);

        $themeData['page_title'] = $album['name'];

        $albumTimelineObj = new \SocialKit\User();
        $albumTimelineObj->setId($album['timeline_id']);
        $album['timeline'] = $albumTimelineObj->getRows();

        $continue = true;

        if ($album['timeline']['post_privacy'] == "following")
        {
            $continue = false;

            if (isLogged())
            {
                if ($albumTimelineObj->isFollowing() or $albumTimelineObj->isAdmin())
                {
                    $continue = true;
                }
            }
        }

        foreach ($album as $key => $value)
        {
            if (! is_array($value))
            {
                $themeData['album_' . $key] = $value;
            }
            else
            {
                foreach ($value as $key2 => $value2)
                {
                    if (! is_array($value2))
                    {
                        $themeData['album_' . $key . '_' . $key2] = $value2;
                    }
                }
            }
        }

        /* */
        if ($album['timeline_id'] === $user['id'])
        {
            $themeData['edit_button'] = \SocialKit\UI::view('album/edit-button');
            $themeData['upload_button'] = \SocialKit\UI::view('album/upload-button');
            $themeData['delete_button'] = \SocialKit\UI::view('album/delete-button');
            $themeData['upload_form'] = \SocialKit\UI::view('album/upload-form');
            $themeData['edit_html'] = \SocialKit\UI::view('album/edit');
        }

        $i = 0;
        $listPhotos = '';

        $albumObj = new \SocialKit\Media();
        $albumObj->setId($album['id']);
        $photos = $albumObj->getRows();

        foreach ($photos['each'] as $photo)
        {
            $themeData['list_photo_story_id'] = $photo['post_id'];
            $themeData['list_photo_url'] = $config['site_url'] . '/' . $photo['url'] . '_100x100.' . $photo['extension'];

            if ($photos['timeline']['id'] == $user['id'])
            {
                $themeData['list_photo_buttons'] = \SocialKit\UI::view('album/list-photo-each-buttons');
            }

            $listPhotos .= \SocialKit\UI::view('album/list-photo-each');
        }
        $themeData['list_photos'] = $listPhotos;

        /* Suggestions */
        $themeData['user_suggestions'] = getUserSuggestions('', 5);
        $themeData['page_suggestions'] = getPageSuggestions('', 5);
        $themeData['group_suggestions'] = getGroupSuggestions('', 5);

        /* Trending */
        $themeData['trendings'] = getTrendings('popular');

        if ($continue)
        {
            $themeData['page_content'] = \SocialKit\UI::view('album/content');
        }
    }
}
?>