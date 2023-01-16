<?php
/* Open chat */
function openChat()
{
    if (! isLogged())
    {
        return false;
    }
    
    if (isset($_GET['a']) && $_GET['a'] == "messages") return false;
    
    if (! isset($_SESSION['receiver_id']) or $_SESSION['receiver_id'] < 1) return false;
    
    $chatRecipientId = (int) $_SESSION['receiver_id'];
    $chatRecipientObj = new \SocialKit\User();
    $chatRecipientObj->setId($chatRecipientId);
    $chatRecipient = $chatRecipientObj->getRows();
    
    if (empty($chatRecipient['id']))
    {
        return false;
    }
    
    return $chatRecipient;
}