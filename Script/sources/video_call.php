<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
if ($user['subscription_plan']['video_call'] == 0)
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}
$themeData['page_title'] = $lang['video_call'];

$callId = (int) $_GET['id'];
$callInfo = getVideoCallInfo($callId, "page");

if ($callInfo) $themeData['page_content'] = $callInfo;
