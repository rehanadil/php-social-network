<?php
if (!isLogged())
{
    $password = trim($_POST['pr_password']);
    $token = isValidPasswordResetToken($_POST['pr_token']);
    
    if ($token
        && is_array($token))
    {
        $md5_password = md5($password);
        $query = $conn->query("UPDATE " . DB_ACCOUNTS . " SET password='$md5_password' WHERE id=" . $token['id'] . " AND banned=0");
        
        if ($query)
        {
            $data = array(
                'status' => 200,
                'url' => smoothLink('index.php?a=welcome')
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();