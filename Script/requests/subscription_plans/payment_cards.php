<?php
userOnly();

if (isset($_GET['plan_id'])
	&& $user['stripe_card_last4'] > 0)
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
		$themeData['is_recurring'] = ($user['stripe_customer_id'] == "") ? 0 : 1;
		$themeData['card_on_file'] = str_replace('{digit}', $user['stripe_card_last4'], $lang['use_card_on_file_ending_digit']);
		$themeData['new_card'] = $lang['use_new_card'];
		$data = array(
			"status" => 200,
			"html" => \SocialKit\UI::view('subscription-plans/payment-cards')
		);
	}
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();