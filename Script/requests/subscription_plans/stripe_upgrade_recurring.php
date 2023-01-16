<?php
userOnly();

use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

if (isset($_POST['plan_id'])
	&& $user['stripe_customer_id'] != ""
	&& $user['stripe_subscription_id'] != "")
{
	$planId = (int) $escapeObj->post('plan_id', 0); // Plan ID
	$planQuery = $conn->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId"); // Select all data of plan

	if ($planId != $user['subscription_plan']['id']
		&& $planQuery->num_rows == 1) // If plan exists, and upgrade plan is not current plan
	{
		$plan = $planQuery->fetch_array(MYSQLI_ASSOC); // Get all data of plan from database

		if ($plan['price'] > $user['subscription_plan']['price'])
		{
			$subscriptionId = $user['stripe_subscription_id'];
			$subscription = \Stripe\Subscription::retrieve($subscriptionId); // Get current subscription
			$subscription->plan = $plan['stripe_id']; // Change plan
			$subscription->save(); // Save new plan to subscription

			if (!empty($user['paypal_agreement_id']))
			{
				$cancelOldPlan = new AgreementStateDescriptor();
				$cancelOldPlan->setNote("Cancelling the old subscription plan");
				$oldAgreement = Agreement::get($user['paypal_agreement_id'], $paypalRest);
				$oldAgreement->suspend($cancelOldPlan, $paypalRest);
			}

			$upgradePlan = $conn->query("UPDATE " . DB_USERS .
				" SET subscription_plan=$planId,
				subscription_time=" . time() . ",
				paypal_agreement_id='',
				payment_method='stripe'
				WHERE id=" . $user['id']); // Update user with customer data and subscription plan

			if ($upgradePlan)
			{
				$conn->query("INSERT INTO " . DB_PLAN_SALES . "
					(user_id,plan_id,time,type)
					VALUES
					(" . $user['id'] . ",$planId," . time() . ",'upgrade')");

				$data = array(
					"status" => 200,
					"url" => smoothLink('index.php?a=subscription-plans')
				);
			}
		}
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();