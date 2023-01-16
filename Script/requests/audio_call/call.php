<?php
userOnly();

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;
use Twilio\Rest\Client;

if (isset($_GET['receiver_id'])
	&& $user['subscription_plan']['audio_call'] == 1)
{
    $receiverId = (int) $_GET['receiver_id'];
    $callerId = $user['id'];

    $receiverObj = new \SocialKit\User();
    $receiverObj->setId($receiverId);
    $receiver = $receiverObj->getRows();

    $callerObj = $userObj;
    $caller = $user;

    if ($callerId !== $receiverId)
    {
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

		    $deleteSql = "DELETE FROM " . DB_AUDIO_CALLS . "
		    	WHERE caller_id=" . $user['id'] . "
		    	OR receiver_id=" . $user['id'];
		    $conn->query($deleteSql);

		    $callInfo = array(
		    	'room' => $roomName,
		    	
		        'caller_id' => $callerId,
		        'caller_access_token' => $escapeObj->stringEscape($callerToken),
		        'caller_call_id' => $callerCallId,

		        'receiver_id' => $receiverId,
		        'receiver_access_token' => $escapeObj->stringEscape($receiverToken),        
		        'receiver_call_id' => $receiverCallId,
		        'time' => time()
		    );
		    
		    $fields = implode(',', array_keys($callInfo));
		    $values = '\'' . implode('\',\'', $callInfo) . '\'';
		    $createSql = "INSERT INTO " . DB_AUDIO_CALLS . " ($fields) VALUES ($values)";
		    $createQuery = $conn->query($createSql);

		    if ($createQuery)
		    {
		    	$callId = $conn->insert_id;
		    	$themeData['call_id'] = $callId;
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
		            'id' => $callId,
		            'html' => \SocialKit\UI::view('popups/audio-calling')
		        );
		    }
		}
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();