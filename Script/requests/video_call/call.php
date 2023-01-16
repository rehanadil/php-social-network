<?php
userOnly();

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;

if (isset($_GET['receiver_id'])
	&& $user['subscription_plan']['video_call'] == 1)
{
    $receiverId = (int) $_GET['receiver_id'];
    $callerId = $user['id'];

    $receiverObj = new \SocialKit\User();
    $receiverObj->setId($receiverId);
    $receiver = $receiverObj->getRows();

    $callerObj = $userObj;
    $caller = $user;

    if ($receiverObj->isReal('user')
    	&& $callerObj->isReal('user'))
    {
	    $accountSid = $config['twilio_video_accountSid'];
	    $apiKeySid = $config['twilio_video_apiKeySid'];
	    $apiKeySecret = $config['twilio_video_apiKeySecret'];
	    $configurationProfileSid = $config['twilio_video_configurationProfileSid'];

	    $client = new Client($apiKeySid, $apiKeySecret);
	    $roomName = md5(time() . $callerId . $receiverId);
		$room = $client->video->rooms->create(array(
			'uniqueName' => $roomName,
			'statusCallback' => $config['site_url']
		));

	    $callerCallId = substr(md5(microtime()), 0, 15);
	    $callerAccessToken = new AccessToken(
		    $accountSid, 
		    $apiKeySid, 
		    $apiKeySecret, 
		    3600, 
		    $callerCallId
		);
		$callerGrant = new VideoGrant();
		$callerGrant->setRoom($roomName);
		$callerAccessToken->addGrant($callerGrant);
	    $callerToken = $callerAccessToken->toJWT();

	    $receiverCallId = substr(md5(time()), 0, 15);
	    $receiverAccessToken = new AccessToken(
		    $accountSid, 
		    $apiKeySid, 
		    $apiKeySecret, 
		    3600, 
		    $receiverCallId
		);
		$receiverGrant = new VideoGrant();
		$receiverGrant->setRoom($roomName);
		$receiverAccessToken->addGrant($receiverGrant);
	    $receiverToken = $receiverAccessToken->toJWT();

	    $createVideoCall = createVideoCall(array(
	    	'room' => $roomName,
	    	
	        'caller_id' => $callerId,
	        'caller_access_token' => $escapeObj->stringEscape($callerToken),
	        'caller_call_id' => $callerCallId,

	        'receiver_id' => $receiverId,
	        'receiver_access_token' => $escapeObj->stringEscape($receiverToken),        
	        'receiver_call_id' => $receiverCallId
	    ));
	    
	    if ($createVideoCall)
	    {
	    	$themeData['call_id'] = $createVideoCall['id'];
	    	$themeData['receiver_id'] = $receiver['id'];
	    	$themeData['receiver_thumbnail_url'] = $receiver['thumbnail_url'];
	    	$themeData['receiver_avatar_url'] = $receiver['avatar_url'];
	    	$themeData['receiver_image_url'] = $receiver['avatar_url'];
	    	$themeData['receiver_name'] = $receiver['name'];
	    	$themeData['receiver_username'] = $receiver['username'];
	    	$themeData['calling_user'] = str_replace('{user}', $receiver['name'], $lang['calling_user']);
	    	$themeData['user_is_being_called'] = str_replace('{user}', '<strong>'.$receiver['name'].'</strong>', $lang['user_is_being_called']);

	        $data = array(
	            'status' => 200,
	            'id' => $createVideoCall['id'],
	            'html' => \SocialKit\UI::view('popups/video-calling')
	        );
	    }
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();