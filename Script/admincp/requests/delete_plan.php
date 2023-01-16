<?php
if (isset($_POST['plan_id'], $_POST['cancel'])) header("Location: subscription_plans.php");

use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

if (isset($_POST['plan_id'], $_POST['delete']))
{
	$planId = (int) $_POST['plan_id'];
	$planSql = $conn->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId");

	if ($planSql->num_rows == 1)
	{
		$plan = $planSql->fetch_array(MYSQLI_ASSOC);

		$subscribersSql = $conn->query("SELECT id,paypal_agreement_id,stripe_subscription_id FROM " . DB_USERS . " WHERE subscription_plan=" . $plan['id']);
		if ($subscribersSql->num_rows > 0)
		{
			while ($subscriber = $subscribersSql->fetch_array(MYSQLI_ASSOC))
			{
				if ($subscriber['paypal_agreement_id'] !== "")
				{
					$cancelPaypal = new AgreementStateDescriptor();
					$cancelPaypal->setNote("Cancelling subscription of " . $plan['name'] . " Plan");
					$agreement = Agreement::get($subscriber['paypal_agreement_id'], $paypalRest);
					$agreement->suspend($cancelPaypal, $paypalRest);
				}

				if ($subscriber['stripe_subscription_id'] !== "")
				{
					$stripeSubscription = \Stripe\Subscription::retrieve($subscriber['stripe_subscription_id']);
					$stripeSubscription->cancel();
				}

				$updateSubscriber = $conn->query("UPDATE " . DB_USERS .
				" SET subscription_plan=" . $defaultPlan['id'] . ",
				stripe_card_last4=0,
				stripe_subscription_id='',
				paypal_agreement_id='',
				payment_method=''
				WHERE id=" . $subscriber['id']);
			}
		}

		$deletePlan = $conn->query("DELETE FROM " . DB_SUBSCRIPTION_PLANS .
			" WHERE id=" . $plan['id']);
		if ($deletePlan) header("Location: subscription_plans.php");
	}
}
?>