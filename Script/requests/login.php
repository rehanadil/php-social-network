<?php
$data['error_message'] = $lang['error_empty_login'];

if (isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
if (isset($_SESSION['user_pass'])) unset($_SESSION['user_pass']);
if (isset($_SESSION['_cache_'])) unset($_SESSION['_cache_']);
if (isset($_SESSION['hook_logic'])) unset($_SESSION['hook_logic']);
if (isset($_SESSION['tempche'])) unset($_SESSION['tempche']);
if (isset($_SESSION['tempche_user_ownfollowing'])) unset($_SESSION['tempche_user_ownfollowing']);
setcookie('sk_u_i', 0, time()-60);
setcookie('sk_u_p', 0, time()-60);

$loginId = $escapeObj->stringEscape($_POST['login_id']);
$loginPassword = trim($_POST['login_password']);
$loginPasswordMd5 = md5($loginPassword);

$userId = getUserId($conn, $loginId);

if ($userId)
{
    $query = $conn->query("SELECT id,username,email_verified,active FROM " . DB_ACCOUNTS . " WHERE id=$userId AND password='$loginPasswordMd5' AND type='user' AND banned=0");
    $data['error_message'] = $lang['error_bad_login'];
    
    if ($query->num_rows == 1)
    {
        $fetch = $query->fetch_array(MYSQLI_ASSOC);
        $continue = true;

        if ($fetch['active'] == 0)
        {
            $conn->query("UPDATE " . DB_ACCOUNTS . " SET active=1 WHERE id=" . $fetch['id']);
        }
        
        if ($config['email_verification'] == 1 && $fetch['email_verified'] == 0)
        {
            $continue = false;
            $data = array(
                "status" => 417,
                "error_type" => "UNVERIFIED"
            );
        }
        
        if ($continue == true)
        {
            $_SESSION['user_id'] = $fetch['id'];
            $_SESSION['user_pass'] = $loginPasswordMd5;
            
            if (isset($_POST['keep_logged_in']) && $_POST['keep_logged_in'] == true)
            {
                setcookie('sk_u_i', $_SESSION['user_id'], time() + (60 * 60 * 24 * 30));
                setcookie('sk_u_p', $_SESSION['user_pass'], time() + (60 * 60 * 24 * 30));
            }
            
            $data = array(
                "status" => 200,
                "redirect_url" => smoothLink('index.php?a=home')
            );

            $conn->query("INSERT INTO " . DB_LOGIN_SESSIONS . " (user_id,time) VALUES (" . $fetch['id'] . "," . time() . ")");
        }
    }
    else
    {
        $data = array(
            "status" => 417,
            "error_type" => "INCORRECT_PASSWORD"
        );
    }
}
else
{
    $data = array(
        "status" => 417,
        "error_type" => "NO_USER_FOUND"
    );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();