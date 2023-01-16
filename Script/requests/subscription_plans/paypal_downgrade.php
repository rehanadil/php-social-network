<?php
userOnly();

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

if (isset($_POST['plan_id']))
{
	$planId = (int) $escapeObj->post('plan_id', 0);
	$planQuery = $conn->query("SELECT id,name,paypal_id,description FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId");

	if ($planQuery->num_rows == 1)
	{
		$plan = $planQuery->fetch_array(MYSQLI_ASSOC);

		$agreement = new Agreement();

		$agreement->setName($plan['name'])
			->setDescription($plan['description'])
			->setStartDate(date('c', (time()+300)));
		
		$paypalPlan = new Plan();
		$paypalPlan->setId($plan['paypal_id']);
		$agreement->setPlan($paypalPlan);
		
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');
		$agreement->setPayer($payer);

		$agreement = $agreement->create($paypalRest);
		$approvalUrl = $agreement->getApprovalLink();

		$data = array(
			"status" => 200,
			"type" => "paypal",
			"url" => $approvalUrl
		);
		$_SESSION['new_plan_id'] = $plan['id'];
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();