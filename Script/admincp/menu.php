<div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="index.php" class="site_title"><i class="fa fa-paw"></i> <span>Admin Panel</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <img src="<?php echo $user['thumbnail_url'] ?>" alt="<?php echo $user['name'] ?>" class="img-circle profile_img">
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $user['name'] ?></h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-bar-chart"></i> Statistics <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="users_statistics.php">Users Statistics</a></li>
                      <li><a href="pages_statistics.php">Pages Statistics</a></li>
                      <li><a href="groups_statistics.php">Groups Statistics</a></li>
                      <li><a href="plan_sales_statistics.php">Plan Sales Statistics</a></li>

                      <?php
                      if ($config['friends'] == 1) {
                      ?>
                      <li><a href="friends_statistics.php">Friends Statistics</a></li>
                      <?php
                      } else {
                      ?>
                      <li><a href="follows_statistics.php">Follows Statistics</a></li>
                      <?php } ?>

                      <li><a href="pagelikes_statistics.php">Page Likes Statistics</a></li>
                      <li><a href="groupjoins_statistics.php">Group Joins Statistics</a></li>

                      <li><a href="stories_statistics.php">Stories Statistics</a></li>
                      <li><a href="comments_statistics.php">Comments Statistics</a></li>
                      <li><a href="reactions_statistics.php">Reactions Statistics</a></li>
                      <li><a href="shares_statistics.php">Shares Statistics</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-gear"></i> Settings <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="settings_website.php">Website</a></li>
                      <li><a href="settings_registration.php">Registration</a></li>
                      <li><a href="settings_social_logins.php">Social Logins</a></li>
                      <li><a href="settings_smtp.php">SMTP/Email</a></li>
                      <li><a href="settings_communicationmethod.php">Communication Method</a></li>
                      <li><a href="settings_video.php">Audio/Video Calls</a></li>
                      <li><a href="settings_uploads.php">Uploads</a></li>
                      <li><a href="settings_payments.php">Payments/Billing</a></li>
                      <li><a href="settings_users.php">Users</a></li>
                      <li><a href="settings_pages.php">Pages</a></li>
                      <li><a href="settings_groups.php">Groups</a></li>
                      <li><a href="settings_events.php">Events</a></li>
                      <li><a href="settings_stories.php">Stories</a></li>
                      <li><a href="settings_comments.php">Comments</a></li>
                      <li><a href="settings_censoredkeywords.php">Censored Keywords</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-laptop"></i> Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="manage_users.php">Manage Users</a></li>
                      <li><a href="manage_pages.php">Manage Pages</a></li>
                      <li><a href="manage_groups.php">Manage Groups</a></li>
                      <li><a href="manage_admins.php">Manage Admins</a></li>
                      <li><a href="manage_announcements.php">Manage Announcements</a></li>
                      <li><a href="manage_ads.php">Manage Advertisements</a></li>
                      <li><a href="manage_plan_sales.php">Manage Plan Sales</a></li>
                      <li><a href="manage_reports.php">Manage Reports</a></li>
                      <li><a href="manage_banned_users.php">Manage Banned Users</a></li>
                      <li><a href="manage_banned_pages.php">Manage Banned Pages</a></li>
                      <li><a href="manage_banned_groups.php">Manage Banned Groups</a></li>
                      <li><a href="manage_banned_ips.php">Manage Banned IPs</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-futbol-o"></i> Themes <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="themes.php">All Themes</a></li>
                      <?php
                      $themes = glob('../themes/*', GLOB_ONLYDIR);
                      
                      foreach ($themes as $theme_url)
                      {
                          include($theme_url . '/info.php');
                          $theme = str_replace('../themes/', '', $theme_url);
                      ?>
                      <li><a href="theme.php?t=<?php echo $theme ?>"><?php echo ucwords($theme) ?></a></li>
                      <?php
                      }
                      ?>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-usd"></i> Subscription Plans <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="subscription_plan_create.php">Create New Plan</a></li>
                      <li><a href="subscription_plans.php">Show All Plans</a></li>
                      <li><a href="subscription_plan_edit.php?default">Edit Default Plan</a></li>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-wrench"></i> Addons <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <?php
                      $addons = glob('../addons/add.*', GLOB_ONLYDIR);
                      
                      foreach ($addons as $addon)
                      {
                          include($addon . '/info.php');
                          $enabled = false;

                          if ( file_exists($addon . '/enabled.html') )
                          {
                              $enabled = true;
                          }
                      ?>
                      <li><a href="addon.php?a=<?php echo str_replace('../addons/', '', $addon) ?>"><?php echo $name ?></a></li>
                      <?php
                      }
                      ?>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-language"></i> Languages <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="create_language.php">Add New Language</a></li>
                      <li><a href="create_keyword.php">Add New Keyword</a></li>
                      <li><a href="import_keywords.php">Import Keywords</a></li>
                      <?php
                      $getAllLanguagesQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES);
                      while ($getLanguage = $getAllLanguagesQuery->fetch_array(MYSQLI_ASSOC))
                      {
                      ?>
                      <li><a href="edit_language.php?id=<?php echo $getLanguage['type'] ?>"><?php echo ucwords($getLanguage['type']) ?></a></li>
                      <?php
                      }
                      ?>
                    </ul>
                  </li>

                  <li><a><i class="fa fa-code-fork"></i> Developers <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="dev_apps.php">Apps</a></li>
                    </ul>
                  </li>
                </ul>
              </div>

            </div>
            <!-- /sidebar menu -->
          </div>
        </div>