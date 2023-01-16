<?php require_once('admincore.php'); ?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" href="<?php echo $config['theme_url'] ?>/images/logo/mini.png">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">

    <title>Subscription Plans</title>

    <link href="https://fonts.googleapis.com/css?family=Lato%3A100%2C300%2C400%2C700+900&#038;subset=latin%2Clatin-ext%2Cvietnamese%2Ccyrillic-ext%2Ccyrillic%2Cgreek%2Cgreek-ext" rel="stylesheet" media="all">

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    
    <!-- Custom Theme Style -->
    <link href="build/css/custom.css" rel="stylesheet">
    <link href="build/css/plan.css" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <?php include_once('menu.php'); ?>

        <!-- top navigation -->
        <?php include_once('top_nav.php'); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title"></div>

            <div class="clearfix"></div>

            <div class="row">
              <?php
              $plansQuery = $conn->query("SELECT * FROM " . DB_SUBSCRIPTION_PLANS . " WHERE is_default=0 ORDER BY price ASC");
              while ($plan = $plansQuery->fetch_array(MYSQLI_ASSOC))
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
                  krsort($val);
                  foreach ($val as $i2 => $val2)
                  {
                    if ($i2 === "boost_posts")
                    {
                      $featureAvailable = $isBoostPosts;
                      $featureName = str_replace('{num}', $boostPosts, $lang['plan_boost_label']);
                    }
                    else
                    {
                      $featureAvailable = $val2;
                      $featureName = $lang['plan_' . $i2 . '_label'];
                    }
                    $featuresHtml .= '<li class="feature" data-available="' . $featureAvailable . '"><i class="fa fa-check"></i> ' . $featureName . '</li>';
                  }
                }

                $currency = Sk_currency($plan['currency']);
                $price = $plan['price'] / 100;
                $billingCycle = $plan['billing_cycle'];
                $color = "#" . $plan['plan_color'];
                $icon = ($plan['plan_icon'] != "") ? $config['site_url'] . '/' . $plan['plan_icon'] : $config['theme_url'] . '/images/default-plan-icon.png';
              ?>
              <div class="planDisplayContainer">
                <div id="plan_<?php echo $plan['id'] ?>" class="planDisplayJar" style="background-color:<?php echo $color ?>;background-image:url('images/brushed-alum.png')">
                  <div class="nameJar">
                    <img class="icon" src="<?php echo $icon ?>">
                    <?php echo $plan['name'] ?>
                  </div>

                  <div>
                    <div class="priceJar" style="color:<?php echo $color ?>;">
                      <span class="currency"><?php echo $currency ?></span>
                      <span class="price"><?php echo $price ?></span>
                      <span class="cycle">/<?php echo $billingCycle ?></span>
                    </div>
                  </div>

                  <ul class="featureJar">
                    <?php echo $featuresHtml ?>
                  </ul>

                  <div class="buyJar">
                    <div class="dropdownJar">
                      <a href="manage_subscribers.php?planid=<?php echo $plan['id'] ?>"><i class="fa fa-list-ul"></i> View Subscribers</a>
                      <a href="subscription_plan_edit.php?id=<?php echo $plan['id'] ?>"><i class="fa fa-pencil"></i> Edit Plan</a>
                      <hr>
                      <a href="subscription_plan_delete.php?id=<?php echo $plan['id'] ?>"><i class="fa fa-times-circle"></i> Delete Plan</a>
                    </div>
                    <button id="plan_{{PLAN_ID}}_btn" style="color:<?php echo $color ?>;"><i class="fa fa-gear"></i> Control</button>
                  </div>
                </div>
              </div>
              <?php
              }
              ?>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <script>
    $(document).on("click", ".buyJar", function(){
        $This = $(this);
        $This.find(".dropdownJar").slideToggle();
    });
    </script>
  </body>
</html>