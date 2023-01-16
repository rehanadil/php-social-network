<?php
userOnly();

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

if (isset($_GET['plan_id']))
{
	$planId = (int) $escapeObj->get('plan_id', 0);
	$planQuery = $conn->query("SELECT id,name,paypal_id,description FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId");

	if ($planQuery->num_rows == 1)
	{
		$plan = $planQuery->fetch_array(MYSQLI_ASSOC);

		$agreementObj = new Agreement();

		$agreementObj->setName($plan['name'])
			->setDescription($plan['description'])
			->setStartDate(date('c', (time() + 10)));
		
		try {
			$paypalPlan = new Plan();
			$paypalPlan->setId($plan['paypal_id']);
			$agreementObj->setPlan($paypalPlan);
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			error_log($ex->getData());
		}

		try {
			$payer = new Payer();
			$payer->setPaymentMethod('paypal');
			$agreementObj->setPayer($payer);
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			error_log($ex->getData());
		}

		try {
			$agreement = $agreementObj->create($paypalRest);
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			error_log($ex->getData());
		}

		try {
			$approvalUrl = $agreement->getApprovalLink();
		} catch (PayPal\Exception\PayPalConnectionException $ex) {
			error_log($ex->getData());
		}

		$data = array(
			"status" => 200,
			"url" => $approvalUrl
		);
		$_SESSION['new_plan_id'] = $plan['id'];
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();