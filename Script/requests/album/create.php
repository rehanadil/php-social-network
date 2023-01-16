<?php
if (isset($_FILES['photos']['name']) && $user['subscription_plan']['create_albums'] == 1)
{
    if (! empty($_POST['album_name']))
    {
        $time = time();
        $name = $escapeObj->stringEscape(__POST__('album_name'));
        $description = $escapeObj->stringEscape(__POST__('album_descr'));

        $photosObj = new \SocialKit\registerMedia();

        $photosObj->setAlbumName($name);
        $photosObj->setAlbumDescription($description);
        $albumId = $photosObj->createAlbum();

        $photosObj->setFile($_FILES['photos']);
        $photos = $photosObj->register();

        foreach ($photos as $i => $photo)
        {
            $photoStorySQL = $conn->query("INSERT INTO " . DB_POSTS . "
                (media_id,time,timeline_id,active,hidden)
                VALUES
                (" . $photo['id'] . ",$time," . $user['id'] . ",1,1)");
            
            if ($photoStorySQL)
            {
                $photoStoryId = $conn->insert_id;
                $conn->query("UPDATE " . DB_POSTS . "
                    SET post_id=id
                    WHERE id=$photoStoryId");
                $conn->query("UPDATE " . DB_MEDIA . "
                    SET post_id=$photoStoryId
                    WHERE id=" . $photo['id']);

                $photoStoryObj = new \SocialKit\Story();
                $photoStoryObj->setId($photoStoryId);
                $photoStoryObj->putFollow();
            }
        }

        $text = '@added_' . count($photos) . '_new_photos_to_album@ [album]' . $albumId . '[/album]';
        $albumStorySQL = $conn->query("INSERT INTO " . DB_POSTS . "
            (active,media_id,activity_text,time,timeline_id)
            VALUES
            (1,$albumId,'$text',$time," . $user['id'] . ")");
        
        if ($albumStorySQL)
        {
            $storyId = $conn->insert_id;
            $conn->query("UPDATE " . DB_POSTS . " SET post_id=$storyId WHERE id=$storyId");
            
            $postObj = new \SocialKit\Story();
            $postObj->setId($storyId);
            $post = $postObj->getRows();

            $postObj->putFollow();

            $data = array(
                'status' => 200,
                'url' => smoothLink('index.php?a=album&b=' . $albumId)
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();