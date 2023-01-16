<?php
require_once('PayPal/vendor/autoload.php');
$paypalRest = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(
  $config['paypal_id'],
  $config['paypal_secret']
));
if ($config['paypal_mode'] === "sandbox")
{
  $paypalRest->setConfig(array(
    'mode' => "sandbox"
  ));
}
else
{
  $paypalRest->setConfig(array(
    'mode' => "live"
  ));
}