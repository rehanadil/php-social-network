<?php
require_once('stripe-php-4.4.0/init.php');
$stripe = array(
  "publishable_key" =>  $config['stripe_publishable_key'],
  "secret_key"      =>  $config['stripe_secret_key']
);

\Stripe\Stripe::setApiKey($config['stripe_secret_key']);