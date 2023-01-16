<?php
userOnly();

use PayPal\Api\Agreement;
use PayPal\Api\AgreementStateDescriptor;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

if (isset($_POST['token_id'])
	&& isset($_POST['token_email'])
	&& isset($_POST['card_last4'])
	&& isset($_POST['plan_id']))
{
	$tokenId = $escapeObj->post('token_id'); // Token ID
	$tokenEmail = $escapeObj->post('token_email'); // Token Email
	$cardLast4 = (int) $escapeObj->post('card_last4', 0); // Last 4 digits of card
	$planId = (int) $escapeObj->post('plan_id', 0); // Plan ID
	$planQuery = $conn->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId"); // Select all data of plan

	if (filter_var($tokenEmail, FILTER_VALIDATE_EMAIL)
		&& $planId !== $user['subscription_plan']['id']
		&& $planQuery->num_rows == 1) // If plan exists, Token Email is valid, and upgrade plan is not current plan
	{
		$plan = $planQuery->fetch_array(MYSQLI_ASSOC); // Get all data of plan from database

		if ($plan['price'] > $user['subscription_plan']['price'])
		{
			if (empty($user['stripe_customer_id'])) // If customer ID is not set
			{
				$customer = \Stripe\Customer::create(array( // Create customer in Stripe
					"email" => $user['email'],
					"source" => $tokenId
				));
				$customerId = $customer->id; // Get customer ID

				$conn->query("UPDATE " . DB_USERS .
				" SET stripe_customer_id='$customerId'
				WHERE id=" . $user['id']); // Update user with subscription ID
			}
			else
			{
				$customerId = $user['stripe_customer_id'];
				$customer = \Stripe\Customer::retrieve($user['stripe_customer_id']); // Retrieve customer from Stripe
				$customer->source = $tokenId; // Apply new credit card info
				$customer->save(); // Save new info
			}

			if (!empty($user['paypal_agreement_id']))
			{
				$cancelOldPlan = new AgreementStateDescriptor();
				$cancelOldPlan->setNote("Cancelling the old subscription plan");
				$oldAgreement = Agreement::get($user['paypal_agreement_id'], $paypalRest);
				$oldAgreement->suspend($cancelOldPlan, $paypalRest);
			}

			if (empty($user['stripe_subscription_id'])) // If subscription ID is not set
			{
				$subscription = \Stripe\Subscription::create(array( // Create a subscription plan for the user
					"customer" => $customerId,
					"plan" => $plan['stripe_id'],
				));
				$subscriptionId = $subscription->id; // Get subscription ID

				$conn->query("UPDATE " . DB_USERS .
				" SET stripe_subscription_id='$subscriptionId'
				WHERE id=" . $user['id']); // Update user with subscription ID
			}
			else
			{
				$subscriptionId = $user['stripe_subscription_id'];
				$subscription = \Stripe\Subscription::retrieve($subscriptionId); // Get current subscription
				$subscription->plan = $plan['stripe_id']; // Change plan
				$subscription->save(); // Save new plan to subscription
			}

			$updateUser = $conn->query("UPDATE " . DB_USERS .
				" SET stripe_customer_email='$tokenEmail',
				stripe_card_last4=$cardLast4,
				paypal_agreement_id='',
				payment_method='stripe',
				subscription_plan=$planId,
				subscription_time=" . time() . "
				WHERE id=" . $user['id']); // Update user with customer data and subscription plan

			if ($updateUser)
			{
				$conn->query("INSERT INTO " . DB_PLAN_SALES . "
					(user_id,plan_id,time,type)
					VALUES
					(" . $user['id'] . ",$planId," . time() . ",'new')");

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