<?php
require_once('admincore.php');

use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

if (isset($_POST['plan_id']))
{
	$planId = (int) $_POST['plan_id'];
	$planSql = $conn->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planId");

	if ($planSql->num_rows == 1)
	{
		$plan = $planSql->fetch_array(MYSQLI_ASSOC);

		$color = str_ireplace('#', '', $escapeObj->post('color', $plan['plan_color']));

		$boostPosts = (int) $escapeObj->post('boost_posts', $plan['boost_posts']);
		$audioCall = (int) $escapeObj->post('audio_call', $plan['audio_call']);
		$videoCall = (int) $escapeObj->post('video_call', $plan['video_call']);
		$addFriends = (int) $escapeObj->post('add_friends', $plan['add_friends']);
		$chat = (int) $escapeObj->post('chat', $plan['chat']);
		$createAlbums = (int) $escapeObj->post('create_albums', $plan['create_albums']);
		$createEvents = (int) $escapeObj->post('create_events', $plan['create_events']);
		$createGroups = (int) $escapeObj->post('create_groups', $plan['create_groups']);
		$createHashtags = (int) $escapeObj->post('create_hashtags', $plan['create_hashtags']);
		$createPages = (int) $escapeObj->post('create_pages', $plan['create_pages']);
		$editStories = (int) $escapeObj->post('edit_stories', $plan['edit_stories']);
		$featured = (int) $escapeObj->post('featured', $plan['featured']);
		$lastSeen = (int) $escapeObj->post('last_seen', $plan['last_seen']);
		$postNewStories = (int) $escapeObj->post('post_new_stories', $plan['post_new_stories']);
		$sendMessages = (int) $escapeObj->post('send_messages', $plan['send_messages']);
		$storyPrivacy = (int) $escapeObj->post('story_privacy', $plan['story_privacy']);
		$updateAvatar = (int) $escapeObj->post('update_avatar', $plan['update_avatar']);
		$updateCover = (int) $escapeObj->post('update_cover', $plan['update_cover']);
		$uploadAudios = (int) $escapeObj->post('upload_audios', $plan['upload_audios']);
		$uploadDocuments = (int) $escapeObj->post('upload_documents', $plan['upload_documents']);
		$uploadPhotos = (int) $escapeObj->post('upload_photos', $plan['upload_photos']);
		$uploadVideos = (int) $escapeObj->post('upload_videos', $plan['upload_videos']);
		$useChatColors = (int) $escapeObj->post('use_chat_colors', $plan['use_chat_colors']);
		$useEmoticons = (int) $escapeObj->post('use_emoticons', $plan['use_emoticons']);
		$verifiedBadge = (int) $escapeObj->post('verified_badge', $plan['verified_badge']);
		$icon = '';

		if ($plan['is_default'] == 0)
		{
			$icon = $plan['plan_icon'];

			if (is_uploaded_file($_FILES['plan_icon']['tmp_name']))
			{
				$ic = $_FILES['plan_icon'];
				$ic_ext = strtolower(pathinfo($ic['name'], PATHINFO_EXTENSION));
				$ims = getimagesize($ic['tmp_name']);
				$ic_mime = strtolower($ims['mime']);

				if ($ic_ext === "png" && $ic_mime === "image/png")
				{
					$ic_name = md5($plan['stripe_id']) . '.png';
					$ic_dir = '../cache/plan_icons/' . $ic_name;

					if (move_uploaded_file($ic['tmp_name'], $ic_dir))
					{
						$icon = 'cache/plan_icons/' . $ic_name;
					}
				}
			}
		}

		$updatePlan = $conn->query("UPDATE " . DB_SUBSCRIPTION_PLANS .
			" SET plan_color='$color',
			plan_icon='$icon',
			boost_posts=$boostPosts,
			audio_call=$audioCall,
			video_call=$videoCall,
			add_friends=$addFriends,
			chat=$chat,
			create_albums=$createAlbums,
			create_events=$createEvents,
			create_groups=$createGroups,
			create_hashtags=$createHashtags,
			create_pages=$createPages,
			edit_stories=$editStories,
			featured=$featured,
			last_seen=$lastSeen,
			post_new_stories=$postNewStories,
			send_messages=$sendMessages,
			story_privacy=$storyPrivacy,
			update_avatar=$updateAvatar,
			update_cover=$updateCover,
			upload_audios=$uploadAudios,
			upload_documents=$uploadDocuments,
			upload_photos=$uploadPhotos,
			upload_videos=$uploadVideos,
			use_chat_colors=$useChatColors,
			use_emoticons=$useEmoticons,
			verified_badge=$verifiedBadge
			WHERE id=" . $plan['id']);

		if ($updatePlan)
		{
			$cacheObj = new \SocialKit\Cache();
			$cacheObj->fromAdminArea(true);
			$cacheObj->clearAll();

			if ($plan['is_default'] == 1)
				header("Location: subscription_plan_edit.php?default");
			else
				header("Location: subscription_plans.php");
		}
	}
}
?>