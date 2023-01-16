<?php
require_once('twilio-php-master/Twilio/autoload.php');
$twilio = array(
  "video_accountSid"      =>  $config['twilio_video_accountSid'],
  "video_configurationProfileSid" =>  $config['twilio_video_configurationProfileSid'],
  "video_apiKeySid" => $config['twilio_video_apiKeySid'],
  "video_apiKeySecret" => $config['twilio_video_apiKeySecret']
);