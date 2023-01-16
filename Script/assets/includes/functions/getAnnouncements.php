<?php
/* Get announcements */
function getAnnouncements() {
    global $conn, $user, $themeData;
    $query = $conn->query("SELECT * FROM " . DB_ANNOUNCEMENTS . "
        WHERE id NOT IN
            (SELECT announcement_id FROM " . DB_ANNOUNCEMENT_VIEWS . "
            WHERE account_id=" . $user['id'] . ")
        ORDER BY id DESC
        LIMIT 1");

    if ($query->num_rows == 1)
    {
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        
        $title = $fetch['title'];
        $title = str_replace(array(
                '{user}'
            ),
            array(
                $user['name']
            ),
            $title);

        $themeData['announcement_id'] = $fetch['id'];
        $themeData['announcement_title'] = $title;
        $themeData['announcement_text'] = html_entity_decode($fetch['text']);
        
        return \SocialKit\UI::view('announcements/content', array(
            'key' => 'announcements_ui_editor',
            'return' => 'string',
            'type' => 'APPEND',
            'content' => array()
        ));
    }
}
