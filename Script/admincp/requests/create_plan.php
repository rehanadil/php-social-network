<?php
require_once('admincore.php');

use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

if (isset($_POST['name'], $_POST['currency'], $_POST['price'], $_POST['billing_cycle']))
{
	$name = $escapeObj->stringEscape($escapeObj->post('name'));
	$description = $escapeObj->postEscape($escapeObj->post('description'));
	$currency = $escapeObj->stringEscape($escapeObj->post('currency', 'usd'));
	$price = $escapeObj->post('price', 0);
	$billingCycle = $escapeObj->stringEscape($escapeObj->post('billing_cycle', 'month'));
	$color = str_ireplace('#', '', $escapeObj->post('color', '#333333'));

	$boostPosts = (int) $escapeObj->post('boost_posts', 1);
	$audioCall = (int) $escapeObj->post('audio_call', 1);
	$videoCall = (int) $escapeObj->post('video_call', 1);
	$addFriends = (int) $escapeObj->post('add_friends', 1);
	$chat = (int) $escapeObj->post('chat', 1);
	$createAlbums = (int) $escapeObj->post('create_albums', 1);
	$createEvents = (int) $escapeObj->post('create_events', 1);
	$createGroups = (int) $escapeObj->post('create_groups', 1);
	$createHashtags = (int) $escapeObj->post('create_hashtags', 1);
	$createPages = (int) $escapeObj->post('create_pages', 1);
	$editStories = (int) $escapeObj->post('edit_stories', 1);
	$featured = (int) $escapeObj->post('featured', 1);
	$lastSeen = (int) $escapeObj->post('last_seen', 1);
	$postNewStories = (int) $escapeObj->post('post_new_stories', 1);
	$sendMessages = (int) $escapeObj->post('send_messages', 1);
	$storyPrivacy = (int) $escapeObj->post('story_privacy', 1);
	$updateAvatar = (int) $escapeObj->post('update_avatar', 1);
	$updateCover = (int) $escapeObj->post('update_cover', 1);
	$uploadAudios = (int) $escapeObj->post('upload_audios', 1);
	$uploadDocuments = (int) $escapeObj->post('upload_documents', 1);
	$uploadPhotos = (int) $escapeObj->post('upload_photos', 1);
	$uploadVideos = (int) $escapeObj->post('upload_videos', 1);
	$useChatColors = (int) $escapeObj->post('use_chat_colors', 1);
	$useEmoticons = (int) $escapeObj->post('use_emoticons', 1);
	$verifiedBadge = (int) $escapeObj->post('verified_badge', 1);

	$name = trim(str_ireplace('plan', '', $name));
	if (strlen($description) > 127) $description = substr($description, 0, 124) . '...';
	$stripeId = strtolower(preg_replace('/[^-A-Za-z0-9]+/', '-', $name)) . '-' . time() . '-plan';
	$price = (int) floor($price * 100);
	$billingCycle = (in_array($billingCycle, array('day','week','month','year'))) ? $billingCycle : 'month';
	$icon = '';

	if (is_uploaded_file($_FILES['plan_icon']['tmp_name']))
	{
		$ic = $_FILES['plan_icon'];
		$ic_ext = strtolower(pathinfo($ic['name'], PATHINFO_EXTENSION));
		$ims = getimagesize($ic['tmp_name']);
		$ic_mime = strtolower($ims['mime']);

		if ($ic_ext === "png" && $ic_mime === "image/png")
		{
			$ic_name = md5($stripeId) . '.png';
			$ic_dir = '../cache/plan_icons/' . $ic_name;

			if (move_uploaded_file($ic['tmp_name'], $ic_dir))
			{
				$icon = 'cache/plan_icons/' . $ic_name;
			}
		}
	}

	$stripePlan = \Stripe\Plan::create(array(
		"name" => $name,
		"id" => $stripeId,
		"interval" => $billingCycle,
		"currency" => $currency,
		"amount" => $price
	));

	$paypalPlan = new Plan();
	$paypalPlan->setName($name)
				->setDescription($description)
				->setType('infinite');
	$paypalCurrency = new Currency(array('value' => ($price/100), 'currency' => strtoupper($currency)));
	$paypalPaymentDefinition = new PaymentDefinition();
	$paypalPaymentDefinition->setName($name)
							->setType('REGULAR')
							->setFrequency(strtoupper($billingCycle))
							->setFrequencyInterval(1)
							->setCycles(0)
							->setAmount($paypalCurrency);
	$paypalMerchantPreferences = new MerchantPreferences();
	$paypalMerchantPreferences->setReturnUrl($config['site_url'] . '/request.php?t=subscription_plans&a=paypal_success')
							->setCancelUrl(smoothLink('index.php?a=subscription-plans&b=upgrade'))
							->setAutoBillAmount("yes");

	$paypalPlan->setPaymentDefinitions(array($paypalPaymentDefinition));
	$paypalPlan->setMerchantPreferences($paypalMerchantPreferences);
	$paypalOutput = $paypalPlan->create($paypalRest);

	if (isset($stripePlan->id)
		&& $paypalOutput->getId())
	{
		$paypalId = $paypalOutput->getId();
		$patch = new Patch();
		$activePatch = new PayPalModel(json_encode(array("state" => "ACTIVE")));
		$patch->setOp('replace')
			->setPath('/')
			->setValue($activePatch);
		$patchRequest = new PatchRequest();
		$patchRequest->addPatch($patch);
		$paypalPlan->update($patchRequest, $paypalRest);

		$insertPlan = $conn->query("INSERT INTO " . DB_SUBSCRIPTION_PLANS
			. " (boost_posts,audio_call,video_call,paypal_id,stripe_id,name,description,currency,price,billing_cycle,add_friends,chat,create_albums,create_events,create_groups,create_hashtags,create_pages,edit_stories,featured,last_seen,post_new_stories,plan_color,plan_icon,send_messages,story_privacy,update_avatar,update_cover,upload_audios,upload_documents,upload_photos,upload_videos,use_chat_colors,use_emoticons)
			VALUES
			($boostPosts,$audioCall,$videoCall,'$paypalId','$stripeId','$name','$description','$currency',$price,'$billingCycle',$addFriends,$chat,$createAlbums,$createEvents,$createGroups,$createHashtags,$createPages,$editStories,$featured,$lastSeen,$postNewStories,'$color','$icon',$sendMessages,$storyPrivacy,$updateAvatar,$updateCover,$uploadAudios,$uploadDocuments,$uploadPhotos,$uploadVideos,$useChatColors,$useEmoticons)");

		if ($insertPlan)
		{
			header("Location: subscription_plans.php");
		}
	}
}
?>