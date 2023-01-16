<?php
if (!isLogged())
{
    $forgotpassId = $escapeObj->stringEscape($_POST['forgotpass_id']);
    $forgotUserId = getUserId($conn, $forgotpassId);

    if (!$forgotUserId)
    {
        $data = array(
            "status" => 417,
            "error_type" => "MAIL_UNKNOWN"
        );
    }

    if ($forgotUserId)
    {
        $query = $conn->query("SELECT id,password,username,email,name FROM " . DB_ACCOUNTS . " WHERE id=$forgotUserId AND type='user' AND active=1 AND banned=0");
        
        if ($query->num_rows == 1)
        {
            $fetch = $query->fetch_array(MYSQLI_ASSOC);
            
            if (isset($fetch['id']))
            {
                $to = $fetch['email'];
                $subject = $config['site_name'] . ' - Password Reset';
                
                $themeData['mail_user_name'] = $fetch['name'];
                $themeData['mail_reset_url'] = smoothLink('index.php?a=welcome&b=password_reset&id=' . $fetch['id'] . '_' . $fetch['password']);
                $to = $fetch['email'];
                
                $message = \SocialKit\UI::view('emails/password-reset-email');
                
                if (sendMail($to, $subject, $message))
                {
                    $data = array(
                        'status' => 200,
                        'message' => $lang['password_reset_mail_confirm']
                    );
                }
            }
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();