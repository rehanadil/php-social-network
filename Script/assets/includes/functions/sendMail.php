<?php
/* Send Mails */
function sendMail($to, $subject, $message)
{
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return "2";

    global $config;

    if ($config['smtp'] == 1)
    {
        $transport = Swift_SmtpTransport::newInstance()
            ->setHost($config['smtp_host'])
            ->setPort($config['smtp_port'])
            ->setUsername($config['smtp_username'])
            ->setPassword($config['smtp_password'])
            ->setEncryption($config['smtp_encryption']);

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance($subject)
            ->setFrom(array($config['email'] => $config['site_name']))
            ->setTo(array($to))
            ->setBody($message, 'text/html');

        $result = $mailer->send($message);

        if ($result) return "1";
    }
    else
    {
        $headers = "From: " . $config['email'] . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $result = mail($to, $subject, $message, $headers);

        if ($result) return "1";
    }
}