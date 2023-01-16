<?php
userOnly();

use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

if (isset($_GET['token'], $_SESSION['new_plan_id'])
	&& $_SESSION['new_plan_id'] > 0)
{
	$planId = (int) $_SESSION['new_plan_id'];
	$planQuery = $conn->query("SELECT id,name,paypal_id,description,price FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId");

	if ($planQuery->num_rows == 1)
	{
		$plan = $planQuery->fetch_array(MYSQLI_ASSOC);
		$type = ($plan['price'] < $user['subscription_plan']['price']) ? "downgrade" : "upgrade";
		$token = $_GET['token'];
		$agreement = new \PayPal\Api\Agreement();
		$agreement->execute($token, $paypalRest);
		$agreementId = $agreement->getId();

		if (!empty($user['paypal_agreement_id']))
		{
			$cancelOldPlan = new AgreementStateDescriptor();
			$cancelOldPlan->setNote("Cancelling the old subscription plan");
			$oldAgreement = Agreement::get($user['paypal_agreement_id'], $paypalRest);
			$oldAgreement->suspend($cancelOldPlan, $paypalRest);
		}

		if (!empty($user['stripe_subscription_id']))
		{
			$stripeSubscription = \Stripe\Subscription::retrieve($user['stripe_subscription_id']);
			$stripeSubscription->cancel();
		}

		$updateUser = $conn->query("UPDATE " . DB_USERS .
		" SET subscription_plan=$planId,
		subscription_time=" . time() . ",
		stripe_card_last4=0,
		stripe_subscription_id='',
		paypal_agreement_id='$agreementId',
		payment_method='paypal'
		WHERE id=" . $user['id']); // Update user with new subscription plan

		if ($updateUser)
		{
			$conn->query("INSERT INTO " . DB_PLAN_SALES . "
				(user_id,plan_id,time,type)
				VALUES
				(" . $user['id'] . ",$planId," . time() . ",'$type')");

			header("Location: " . smoothLink('index.php?a=subscription-plans'));
		}
	}

	unset($_SESSION['new_plan_id']);
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();