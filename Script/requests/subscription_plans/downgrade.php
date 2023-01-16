<?php
userOnly();

if ($user['payment_method'] === "paypal")
{
	include_once('paypal_downgrade.php');
}
elseif ($user['payment_method'] === "stripe")
{
	include_once('stripe_downgrade.php');
}