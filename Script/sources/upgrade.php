<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}

$themeData['page_title'] = $lang['subscription_plans_page_title'];

$plansHtml = '';
$plans = $conn->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE is_default=0 ORDER BY price ASC");
while ($plan = $plans->fetch_array(MYSQLI_ASSOC))
{
	$addFriends = (int) $plan['add_friends'];
	$boostPosts = (int) $plan['boost_posts'];
	$audioCall = (int) $plan['audio_call'];
	$videoCall = (int) $plan['video_call'];
	$chat = (int) $plan['chat'];
	$createAlbums = (int) $plan['create_albums'];
	$createEvents = (int) $plan['create_events'];
	$createGroups = (int) $plan['create_groups'];
	$createHashtags = (int) $plan['create_hashtags'];
	$createPages = (int) $plan['create_pages'];
	$editStories = (int) $plan['edit_stories'];
	$featured = (int) $plan['featured'];
	$lastSeen = (int) $plan['last_seen'];
	$postNewStories = (int) $plan['post_new_stories'];
	$sendMessages = (int) $plan['send_messages'];
	$storyPrivacy = (int) $plan['story_privacy'];
	$updateAvatar = (int) $plan['update_avatar'];
	$updateCover = (int) $plan['update_cover'];
	$uploadAudios = (int) $plan['upload_audios'];
	$uploadDocuments = (int) $plan['upload_documents'];
	$uploadPhotos = (int) $plan['upload_photos'];
	$uploadVideos = (int) $plan['upload_videos'];
	$useChatColors = (int) $plan['use_chat_colors'];
	$useEmoticons = (int) $plan['use_emoticons'];
	$verifiedBadge = (int) $plan['verified_badge'];
	$isBoostPosts = ($boostPosts == 0) ? 0 : 1;

	$featuresHtml = '';

	$features = array();
	$features[$addFriends]['add_friends'] = $addFriends;
	$features[$isBoostPosts]['boost_posts'] = $boostPosts;
	$features[$audioCall]['audio_call'] = $audioCall;
	$features[$videoCall]['video_call'] = $videoCall;
	$features[$chat]['chat'] = $chat;
	$features[$createAlbums]['create_albums'] = $createAlbums;
	$features[$createEvents]['create_events'] = $createEvents;
	$features[$createGroups]['create_groups'] = $createGroups;
	$features[$createHashtags]['create_hashtags'] = $createHashtags;
	$features[$createPages]['create_pages'] = $createPages;
	$features[$editStories]['edit_stories'] = $editStories;
	$features[$featured]['featured'] = $featured;
	$features[$lastSeen]['last_seen'] = $lastSeen;
	$features[$postNewStories]['post_new_stories'] = $postNewStories;
	$features[$sendMessages]['send_messages'] = $sendMessages;
	$features[$storyPrivacy]['story_privacy'] = $storyPrivacy;
	$features[$updateAvatar]['update_avatar'] = $updateAvatar;
	$features[$updateCover]['update_cover'] = $updateCover;
	$features[$uploadAudios]['upload_audios'] = $uploadAudios;
	$features[$uploadDocuments]['upload_documents'] = $uploadDocuments;
	$features[$uploadPhotos]['upload_photos'] = $uploadPhotos;
	$features[$uploadVideos]['upload_videos'] = $uploadVideos;
	$features[$useChatColors]['use_chat_colors'] = $useChatColors;
	$features[$useEmoticons]['use_emoticons'] = $useEmoticons;
	$features[$verifiedBadge]['verified_badge'] = $verifiedBadge;

	krsort($features);

	foreach ($features as $i => $val)
	{
		arsort($val);
		foreach ($val as $i2 => $val2)
		{
			if ($i2 === "boost_posts")
			{
				$themeData['available'] = $isBoostPosts;
				$themeData['name'] = str_replace('{num}', $boostPosts, $lang['plan_boost_label']);
			}
			else
			{
				$themeData['available'] = $val2;
				$themeData['name'] = $lang['plan_' . $i2 . '_label'];
			}
			$featuresHtml .= \SocialKit\UI::view('subscription-plans/plan-feature');
		}
	}

	$themeData['features'] = $featuresHtml;

	$themeData['plan_id'] = $plan['id'];
	$themeData['name'] = $plan['name'];
	$themeData['currency'] = Sk_currency($plan['currency']);
	$themeData['price'] = $plan['price'] / 100;
	$themeData['billing_cycle'] = $plan['billing_cycle'];
	$themeData['color'] = "#" . $plan['plan_color'];
	$themeData['icon'] = ($plan['plan_icon'] !== "") ? $config['site_url'] . '/' . $plan['plan_icon'] : $config['theme_url'] . '/images/default-plan-icon.png';
	$themeData['default_icon'] = ($plan['plan_icon'] != "") ? 0 : 1;

	$themeData['is_current'] = ($plan['id'] == $user['subscription_plan']['id']) ? 1 : 0;

	$themeData['button'] = ($plan['id'] == $user['subscription_plan']['id']) ? \SocialKit\UI::view('subscription-plans/plan-btn-activated') : (($plan['price'] >= $user['subscription_plan']['price']) ? \SocialKit\UI::view('subscription-plans/plan-btn-upgrade') : \SocialKit\UI::view('subscription-plans/plan-btn-downgrade'));

	$plansHtml .= \SocialKit\UI::view('subscription-plans/plan');
}

$themeData['plans'] = $plansHtml;
$themeData['page_content'] = \SocialKit\UI::view('subscription-plans/upgrade');
