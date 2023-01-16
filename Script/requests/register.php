<?php
$proceed = false;

if (isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
if (isset($_SESSION['user_pass'])) unset($_SESSION['user_pass']);
if (isset($_SESSION['_cache_'])) unset($_SESSION['_cache_']);
if (isset($_SESSION['hook_logic'])) unset($_SESSION['hook_logic']);
if (isset($_SESSION['tempche'])) unset($_SESSION['tempche']);
if (isset($_SESSION['tempche_user_ownfollowing'])) unset($_SESSION['tempche_user_ownfollowing']);
setcookie('sk_u_i', 0, time()-60);
setcookie('sk_u_p', 0, time()-60);

if ($config['captcha'] == 0)
{
    $proceed = true;
}
elseif (isset($_POST['g-recaptcha-response']))
{
    $data = array(
        "status" => 417,
        "error_type" => "BAD_CAPTCHA"
    );
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $recaptchaResult = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $config['recaptcha_secret_key'] . "&response={$recaptchaResponse}");
    $recaptchaResultData = json_decode($recaptchaResult, true);

    if ($recaptchaResultData['success']) $proceed = true;
}

if (!preg_match('/^([-A-Za-z0-9_+.]+)\@([-A-Za-z0-9.]+)$/i', $_POST['email']))
{
    $proceed = false;
    $data = array(
        "status" => 417,
        "error_type" => "INVALID_EMAIL"
    );
}

if ($proceed)
{
    if (validateUsername($_POST['username']))
    {
        $isUsernameExists = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE username='" . $escapeObj->stringEscape($_POST['username']) . "'");
        if ($isUsernameExists->num_rows > 0)
        {
            $proceed = false;
            $data = array(
                "status" => 417,
                "error_type" => "USERNAME_EXISTS"
            );
        }
    }
    else
    {
        $proceed = false;
        $data = array(
            "status" => 417,
            "error_type" => "INVALID_USERNAME"
        );
    }
}

if ($proceed)
{
    $mailQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE email='" . $escapeObj->stringEscape($_POST['email']) . "'");
    if ($mailQuery->num_rows > 0)
    {
        $proceed = false;
        $data = array(
            "status" => 417,
            "error_type" => "EMAIL_EXISTS"
        );
    }
}

if ($proceed)
{
    $registerObj = new \SocialKit\registerUser();
    $registerObj->setUsername($_POST['username']);
    $registerObj->setName($_POST['name']);
    $registerObj->setPassword($_POST['password']);
    $registerObj->setEmail($_POST['email']);
    $registerObj->setGender($_POST['gender']);
    $registerObj->setBirthday($_POST['birthday']);
    
    if ($register = $registerObj->register())
    {
        $to = $register['email'];
        $subject = $config['site_name'] . ' - Email Verification';
        
        $themeData['mail_user_name'] = $register['name'];
        $themeData['mail_verify_link'] = urlencode($config['site_url'] . '/?a=email-verification&email=' . $register['email'] . '&key=' . $register['email_verification_key']);
        $message = \SocialKit\UI::view('emails/email-verification');

        sendMail($to, $subject, $message);
        
    	if ($config['email_verification'])
        {
            $data = array(
                "status" => 417,
                "error_type" => "VERIFY_EMAIL"
            );
        }
        else
        {
            $_SESSION['user_id'] = $register['id'];
            $_SESSION['user_pass'] = md5(trim($_POST['password']));

            $data = array(
                "status" => 200,
                "redirect_url" => smoothLink('index.php?a=start')
            );
        }
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();