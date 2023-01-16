<?php
userOnly();

if (isset($_GET['plan_id']))
{
	$planId = (int) $escapeObj->get('plan_id', 0);
	$planQuery = $conn->query("SELECT id,name,price,plan_icon FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId");

	if ($planQuery->num_rows == 1)
	{
		$plan = $planQuery->fetch_array(MYSQLI_ASSOC);

		$themeData['stripe_key'] = $config['stripe_publishable_key'];
		$themeData['plan_id'] = $plan['id'];
		$themeData['plan_name'] = $plan['name'] . ' ' . $lang['plan_label'];
		$themeData['plan_price'] = $plan['price'];
		$themeData['plan_icon'] = ($plan['plan_icon'] != "") ? $config['site_url'] . '/' . $plan['plan_icon'] : $config['theme_url'] . '/images/default-plan-icon.png';
		$themeData['plan_default_icon'] = ($plan['plan_icon'] != "") ? 0 : 1;
		$themeData['is_recurring'] = ($user['stripe_subscription_id'] == "") ? 0 : 1;
		$themeData['stripe_handler'] = \SocialKit\UI::view('subscription-plans/stripe-handler');

		$data = array(
			"status" => 200,
			"html" => \SocialKit\UI::view('subscription-plans/payment-methods')
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();