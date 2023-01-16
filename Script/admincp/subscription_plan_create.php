<?php
include_once('requests/create_plan.php');
?><!DOCTYPE html>
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

    <title>Create New Plan</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">
    <!-- bootstrap-wysiwyg -->
    <link href="vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet">
    <!-- Switchery -->
    <link href="vendors/switchery/dist/switchery.min.css" rel="stylesheet">
    <!-- starrr -->
    <link href="vendors/starrr/dist/starrr.css" rel="stylesheet">
    <!-- Bootstrap Colorpicker -->
    <link href="vendors/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <!-- Fileinput -->
    <link href="vendors/fileinput/css/fileinput.css" rel="stylesheet" media="all">
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- Ion.RangeSlider -->
    <link href="vendors/normalize-css/normalize.css" rel="stylesheet">
    <link href="vendors/ion.rangeSlider/css/ion.rangeSlider.css" rel="stylesheet">
    <link href="vendors/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.css" rel="stylesheet">
    <style>
    .planiconJar {
      width: 200px;
      display: inline-block;
    }
    .planiconJar .file-preview-frame, .planiconJar .file-preview-frame:hover {
      margin: 0;
      padding: 0;
      border: none;
      box-shadow: none;
    }
    .planiconJar .file-preview-frame, .planiconJar .file-preview-frame:hover {
      text-align: center;
    }
    .planiconJar .file-input {
      display: table-cell;
    }
    .planiconJar .file-input {
      max-width: 220px;
      text-align: center;
    }
    </style>
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
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Create New Subscription Plan</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left" method="post" action="?" enctype="multipart/form-data">
                      
                      <div class="form-group">
                        <label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Name <span class="required">*</span> <br><small>Name of your new subscription plan</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="name" name="name" required="required" class="form-control col-md-7 col-xs-12" type="text" onkeyup="createPlanId(this.value);">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="description" class="control-label col-md-3 col-sm-3 col-xs-12">Description <span class="required">*</span> <br><small>Description of your new subscription plan</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea id="description" name="description" required="required" class="form-control col-md-7 col-xs-12"></textarea>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="currency" class="control-label col-md-3 col-sm-3 col-xs-12">Currency <span class="required">*</span> <br><small>Currency of your new subscription plan</small></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="currency" name="currency" required="required" class="select2_single form-control" tabindex="-1">
                            <?php
                            foreach (Sk_currency() as $code => $symbol)
                            {
                            ?>
                            <option value="<?php echo $code ?>" onclick="changeCurrency(this);"><?php echo strtoupper($code) ?></option>
                            <?php
                            }
                            ?>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="price" class="control-label col-md-3 col-sm-3 col-xs-12">Price <span class="required">*</span> <br><small>Price of your new subscription plan</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="price" name="price" placeholder="0.00" step="0.01" required="required" min="0" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="billing_cycle" class="control-label col-md-3 col-sm-3 col-xs-12">Billing Cycle <span class="required">*</span> <br><small>Billing cycle of your new subscription plan</small></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="billing_cycle" name="billing_cycle" required="required" class="select2_single form-control" tabindex="-1">
                            <option value="month">Monthly</option>
                            <option value="day">Daily</option>
                            <option value="week">Weekly</option>
                            <option value="year">Yearly</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="color" class="control-label col-md-3 col-sm-3 col-xs-12">Color <span class="required">*</span> <br><small>Color of your new subscription plan</small></label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="input-group color-input">
                            <input id="color" name="color" required="required" value="#333333" class="form-control" type="text">
                            <span class="input-group-addon"><i></i></span>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="plan_icon" class="control-label col-md-3 col-sm-3 col-xs-12">Icon <span class="required">*</span> <br><small>128 x 128</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="plan-icon-errors-1" class="center-block" style="width:800px;display:none"></div>
                          <div class="planiconJar">
                            <input id="plan-icon" name="plan_icon" type="file" class="file-loading">
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="boost_posts" class="control-label col-md-3 col-sm-3 col-xs-12">Boost Posts <span class="required">*</span> <br><small>(Maximum number of boost-able posts)</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input type="number" id="boost_posts" name="boost_posts" placeholder="0" step="1" required="required" min="0" class="form-control col-md-7 col-xs-12" value="0">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="featured" class="control-label col-md-3 col-sm-3 col-xs-12">Featured <span class="required">*</span> <br><small>Will subscribers of <span class="planName">this plan</span> be featured around the site?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="featured" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="featured" value="1" checked="yes"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="featured" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="audio_call" class="control-label col-md-3 col-sm-3 col-xs-12">Audio Call <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan make audio calls?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="audio_call" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="audio_call" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="audio_call" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="video_call" class="control-label col-md-3 col-sm-3 col-xs-12">Video Call <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan make video calls?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="video_call" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="video_call" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="video_call" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="add_friends" class="control-label col-md-3 col-sm-3 col-xs-12">Add Friends <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan add friends?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="add_friends" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="add_friends" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="add_friends" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="chat" class="control-label col-md-3 col-sm-3 col-xs-12">Chat <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan chat with others and see who's online?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="chat" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="chat" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="chat" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="create_albums" class="control-label col-md-3 col-sm-3 col-xs-12">Create Albums <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan create albums?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="create_albums" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_albums" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_albums" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="create_events" class="control-label col-md-3 col-sm-3 col-xs-12">Create Events <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan create events?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="create_events" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_events" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_events" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="create_groups" class="control-label col-md-3 col-sm-3 col-xs-12">Create Groups <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan create groups?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="create_groups" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_groups" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_groups" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="create_hashtags" class="control-label col-md-3 col-sm-3 col-xs-12">Create Hashtags <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan create hashtags?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="create_hashtags" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_hashtags" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_hashtags" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="create_pages" class="control-label col-md-3 col-sm-3 col-xs-12">Create Pages <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan create pages?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="create_pages" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_pages" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="create_pages" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="edit_stories" class="control-label col-md-3 col-sm-3 col-xs-12">Edit Stories <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan edit stories?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="edit_stories" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="edit_stories" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="edit_stories" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="last_seen" class="control-label col-md-3 col-sm-3 col-xs-12">Show/Hide Last Seen <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan show & hide their last seen?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="last_seen" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="last_seen" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="last_seen" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="post_new_stories" class="control-label col-md-3 col-sm-3 col-xs-12">Post New Stories <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan post new stories?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="post_new_stories" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="post_new_stories" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="post_new_stories" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="send_messages" class="control-label col-md-3 col-sm-3 col-xs-12">Send Messages <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan send messages?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="send_messages" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="send_messages" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="send_messages" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="story_privacy" class="control-label col-md-3 col-sm-3 col-xs-12">Story Privacy <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan change the privacy of their stories?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="story_privacy" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="story_privacy" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="story_privacy" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="update_avatar" class="control-label col-md-3 col-sm-3 col-xs-12">Update Avatar <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan change their avatar?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="update_avatar" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="update_avatar" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="update_avatar" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="update_cover" class="control-label col-md-3 col-sm-3 col-xs-12">Update Cover <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan change their cover?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="update_cover" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="update_cover" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="update_cover" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="upload_audios" class="control-label col-md-3 col-sm-3 col-xs-12">Upload Audios <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan upload audio files?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="upload_audios" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_audios" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_audios" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="upload_documents" class="control-label col-md-3 col-sm-3 col-xs-12">Upload Documents <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan upload documents?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="upload_documents" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_documents" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_documents" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="upload_photos" class="control-label col-md-3 col-sm-3 col-xs-12">Upload Photos <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan upload photos?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="upload_photos" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_photos" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_photos" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="upload_videos" class="control-label col-md-3 col-sm-3 col-xs-12">Upload Videos <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan upload videos?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="upload_videos" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_videos" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="upload_videos" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="use_chat_colors" class="control-label col-md-3 col-sm-3 col-xs-12">Use Chat Colors <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan use and change their chat colors?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="use_chat_colors" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="use_chat_colors" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="use_chat_colors" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="use_emoticons" class="control-label col-md-3 col-sm-3 col-xs-12">Use Emoticons <span class="required">*</span> <br><small>Can subscribers of <span class="planName">this</span> plan use emoticons?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="use_emoticons" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="use_emoticons" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="use_emoticons" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="verified_badge" class="control-label col-md-3 col-sm-3 col-xs-12">Verified Badge <span class="required">*</span> <br><small>Do subscribers of <span class="planName">this</span> plan get a verified badge next to their name?</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="verified_badge" class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="verified_badge" checked="yes" value="1"> &nbsp; Yes &nbsp;
                            </label>
                            <label class="btn btn-default" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="verified_badge" value="0"> &nbsp; No &nbsp;
                            </label>
                          </div>
                        </div>
                      </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button type="submit" class="btn btn-primary">Cancel</button>
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <?php include_once('footer.php'); ?>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="vendors/iCheck/icheck.min.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- Ion.RangeSlider -->
    <script src="vendors/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <!-- Fileinput -->
    <script src="vendors/fileinput/js/fileinput.js"></script>

    <!-- Bootstrap Colorpicker -->
    <script src="vendors/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    
    <!-- bootstrap-wysiwyg -->
    <script src="vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
    <script src="vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
    <script src="vendors/google-code-prettify/src/prettify.js"></script>
    <!-- jQuery Tags Input -->
    <script src="vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <!-- Switchery -->
    <script src="vendors/switchery/dist/switchery.min.js"></script>
    <!-- Select2 -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <!-- Parsley -->
    <script src="vendors/parsleyjs/dist/parsley.min.js"></script>
    <!-- Autosize -->
    <script src="vendors/autosize/dist/autosize.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    <!-- starrr -->
    <script src="vendors/starrr/dist/starrr.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <script>$('.color-input').colorpicker();</script>

    <script>
    $("#plan-icon").fileinput({
        overwriteInitial: true,
        minImageWidth: 128,
        minImageHeight: 128,
        maxImageWidth: 128,
        maxImageHeight: 128,
        showClose: false,
        showCaption: false,
        browseLabel: '',
        removeLabel: '',
        browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#plan-icon-errors-1',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: '<img src="<?php echo $config['theme_url'] . '/images/default-plan-icon.png' ?>" style="width:128px">',
        layoutTemplates: {
          main2: '{preview} {remove} {browse}',
          footer: ''
        },
        allowedFileExtensions: ["jpg", "png", "gif"]
    });
    </script>

    <script>
      $(document).ready(function() {
        $("#range").ionRangeSlider({
          hide_min_max: true,
          keyboard: true,
          min: 50,
          max: 10000,
          from: <?php echo ($config['story_character_limit'] == 0) ? 10000 : $config['story_character_limit']; ?>,
          step: 50,
          grid: true,
          force_edges: true
        });
      });
    </script>

    <!-- bootstrap-daterangepicker -->
    <script>
      $(document).ready(function() {
        $('#birthday').daterangepicker({
          singleDatePicker: true,
          calender_style: "picker_4"
        }, function(start, end, label) {
          console.log(start.toISOString(), end.toISOString(), label);
        });
      });
    </script>
    <!-- /bootstrap-daterangepicker -->

    <!-- bootstrap-wysiwyg -->
    <script>
      $(document).ready(function() {
        function initToolbarBootstrapBindings() {
          var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
              'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
              'Times New Roman', 'Verdana'
            ],
            fontTarget = $('[title=Font]').siblings('.dropdown-menu');
          $.each(fonts, function(idx, fontName) {
            fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
          });
          $('a[title]').tooltip({
            container: 'body'
          });
          $('.dropdown-menu input').click(function() {
              return false;
            })
            .change(function() {
              $(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');
            })
            .keydown('esc', function() {
              this.value = '';
              $(this).change();
            });

          $('[data-role=magic-overlay]').each(function() {
            var overlay = $(this),
              target = $(overlay.data('target'));
            overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
          });

          if ("onwebkitspeechchange" in document.createElement("input")) {
            var editorOffset = $('#editor').offset();

            $('.voiceBtn').css('position', 'absolute').offset({
              top: editorOffset.top,
              left: editorOffset.left + $('#editor').innerWidth() - 35
            });
          } else {
            $('.voiceBtn').hide();
          }
        }

        function showErrorAlert(reason, detail) {
          var msg = '';
          if (reason === 'unsupported-file-type') {
            msg = "Unsupported format " + detail;
          } else {
            console.log("error uploading file", reason, detail);
          }
          $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
            '<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
        }

        initToolbarBootstrapBindings();

        $('#editor').wysiwyg({
          fileUploadError: showErrorAlert
        });

        window.prettyPrint;
        prettyPrint();
      });
    </script>
    <!-- /bootstrap-wysiwyg -->

    <!-- Select2 -->
    <script>
      $(document).ready(function() {
        $(".select2_single").select2({
          allowClear: true
        });
        $(".select2_group").select2({});
        $(".select2_multiple").select2({
          maximumSelectionLength: 4,
          placeholder: "With Max Selection limit 4",
          allowClear: true
        });
      });
    </script>
    <!-- /Select2 -->

    <!-- jQuery Tags Input -->
    <script>
      function onAddTag(tag) {
        alert("Added a tag: " + tag);
      }

      function onRemoveTag(tag) {
        alert("Removed a tag: " + tag);
      }

      function onChangeTag(input, tag) {
        alert("Changed a tag: " + tag);
      }

      $(document).ready(function() {
        $('#tags_1').tagsInput({
          width: 'auto'
        });
      });
    </script>
    <!-- /jQuery Tags Input -->

    <!-- Parsley -->
    <script>
      $(document).ready(function() {
        $.listen('parsley:field:validate', function() {
          validateFront();
        });
        $('#demo-form .btn').on('click', function() {
          $('#demo-form').parsley().validate();
          validateFront();
        });
        var validateFront = function() {
          if (true === $('#demo-form').parsley().isValid()) {
            $('.bs-callout-info').removeClass('hidden');
            $('.bs-callout-warning').addClass('hidden');
          } else {
            $('.bs-callout-info').addClass('hidden');
            $('.bs-callout-warning').removeClass('hidden');
          }
        };
      });

      $(document).ready(function() {
        $.listen('parsley:field:validate', function() {
          validateFront();
        });
        $('#demo-form2 .btn').on('click', function() {
          $('#demo-form2').parsley().validate();
          validateFront();
        });
        var validateFront = function() {
          if (true === $('#demo-form2').parsley().isValid()) {
            $('.bs-callout-info').removeClass('hidden');
            $('.bs-callout-warning').addClass('hidden');
          } else {
            $('.bs-callout-info').addClass('hidden');
            $('.bs-callout-warning').removeClass('hidden');
          }
        };
      });
      try {
        hljs.initHighlightingOnLoad();
      } catch (err) {}
    </script>
    <!-- /Parsley -->

    <!-- Autosize -->
    <script>
      $(document).ready(function() {
        autosize($('.resizable_textarea'));
      });
    </script>
    <!-- /Autosize -->

    <!-- jQuery autocomplete -->
    <script>
      $(document).ready(function() {
        var countries = { AD:"Andorra",A2:"Andorra Test",AE:"United Arab Emirates",AF:"Afghanistan",AG:"Antigua and Barbuda",AI:"Anguilla",AL:"Albania",AM:"Armenia",AN:"Netherlands Antilles",AO:"Angola",AQ:"Antarctica",AR:"Argentina",AS:"American Samoa",AT:"Austria",AU:"Australia",AW:"Aruba",AX:"Åland Islands",AZ:"Azerbaijan",BA:"Bosnia and Herzegovina",BB:"Barbados",BD:"Bangladesh",BE:"Belgium",BF:"Burkina Faso",BG:"Bulgaria",BH:"Bahrain",BI:"Burundi",BJ:"Benin",BL:"Saint Barthélemy",BM:"Bermuda",BN:"Brunei",BO:"Bolivia",BQ:"British Antarctic Territory",BR:"Brazil",BS:"Bahamas",BT:"Bhutan",BV:"Bouvet Island",BW:"Botswana",BY:"Belarus",BZ:"Belize",CA:"Canada",CC:"Cocos [Keeling] Islands",CD:"Congo - Kinshasa",CF:"Central African Republic",CG:"Congo - Brazzaville",CH:"Switzerland",CI:"Côte d’Ivoire",CK:"Cook Islands",CL:"Chile",CM:"Cameroon",CN:"China",CO:"Colombia",CR:"Costa Rica",CS:"Serbia and Montenegro",CT:"Canton and Enderbury Islands",CU:"Cuba",CV:"Cape Verde",CX:"Christmas Island",CY:"Cyprus",CZ:"Czech Republic",DD:"East Germany",DE:"Germany",DJ:"Djibouti",DK:"Denmark",DM:"Dominica",DO:"Dominican Republic",DZ:"Algeria",EC:"Ecuador",EE:"Estonia",EG:"Egypt",EH:"Western Sahara",ER:"Eritrea",ES:"Spain",ET:"Ethiopia",FI:"Finland",FJ:"Fiji",FK:"Falkland Islands",FM:"Micronesia",FO:"Faroe Islands",FQ:"French Southern and Antarctic Territories",FR:"France",FX:"Metropolitan France",GA:"Gabon",GB:"United Kingdom",GD:"Grenada",GE:"Georgia",GF:"French Guiana",GG:"Guernsey",GH:"Ghana",GI:"Gibraltar",GL:"Greenland",GM:"Gambia",GN:"Guinea",GP:"Guadeloupe",GQ:"Equatorial Guinea",GR:"Greece",GS:"South Georgia and the South Sandwich Islands",GT:"Guatemala",GU:"Guam",GW:"Guinea-Bissau",GY:"Guyana",HK:"Hong Kong SAR China",HM:"Heard Island and McDonald Islands",HN:"Honduras",HR:"Croatia",HT:"Haiti",HU:"Hungary",ID:"Indonesia",IE:"Ireland",IL:"Israel",IM:"Isle of Man",IN:"India",IO:"British Indian Ocean Territory",IQ:"Iraq",IR:"Iran",IS:"Iceland",IT:"Italy",JE:"Jersey",JM:"Jamaica",JO:"Jordan",JP:"Japan",JT:"Johnston Island",KE:"Kenya",KG:"Kyrgyzstan",KH:"Cambodia",KI:"Kiribati",KM:"Comoros",KN:"Saint Kitts and Nevis",KP:"North Korea",KR:"South Korea",KW:"Kuwait",KY:"Cayman Islands",KZ:"Kazakhstan",LA:"Laos",LB:"Lebanon",LC:"Saint Lucia",LI:"Liechtenstein",LK:"Sri Lanka",LR:"Liberia",LS:"Lesotho",LT:"Lithuania",LU:"Luxembourg",LV:"Latvia",LY:"Libya",MA:"Morocco",MC:"Monaco",MD:"Moldova",ME:"Montenegro",MF:"Saint Martin",MG:"Madagascar",MH:"Marshall Islands",MI:"Midway Islands",MK:"Macedonia",ML:"Mali",MM:"Myanmar [Burma]",MN:"Mongolia",MO:"Macau SAR China",MP:"Northern Mariana Islands",MQ:"Martinique",MR:"Mauritania",MS:"Montserrat",MT:"Malta",MU:"Mauritius",MV:"Maldives",MW:"Malawi",MX:"Mexico",MY:"Malaysia",MZ:"Mozambique",NA:"Namibia",NC:"New Caledonia",NE:"Niger",NF:"Norfolk Island",NG:"Nigeria",NI:"Nicaragua",NL:"Netherlands",NO:"Norway",NP:"Nepal",NQ:"Dronning Maud Land",NR:"Nauru",NT:"Neutral Zone",NU:"Niue",NZ:"New Zealand",OM:"Oman",PA:"Panama",PC:"Pacific Islands Trust Territory",PE:"Peru",PF:"French Polynesia",PG:"Papua New Guinea",PH:"Philippines",PK:"Pakistan",PL:"Poland",PM:"Saint Pierre and Miquelon",PN:"Pitcairn Islands",PR:"Puerto Rico",PS:"Palestinian Territories",PT:"Portugal",PU:"U.S. Miscellaneous Pacific Islands",PW:"Palau",PY:"Paraguay",PZ:"Panama Canal Zone",QA:"Qatar",RE:"Réunion",RO:"Romania",RS:"Serbia",RU:"Russia",RW:"Rwanda",SA:"Saudi Arabia",SB:"Solomon Islands",SC:"Seychelles",SD:"Sudan",SE:"Sweden",SG:"Singapore",SH:"Saint Helena",SI:"Slovenia",SJ:"Svalbard and Jan Mayen",SK:"Slovakia",SL:"Sierra Leone",SM:"San Marino",SN:"Senegal",SO:"Somalia",SR:"Suriname",ST:"São Tomé and Príncipe",SU:"Union of Soviet Socialist Republics",SV:"El Salvador",SY:"Syria",SZ:"Swaziland",TC:"Turks and Caicos Islands",TD:"Chad",TF:"French Southern Territories",TG:"Togo",TH:"Thailand",TJ:"Tajikistan",TK:"Tokelau",TL:"Timor-Leste",TM:"Turkmenistan",TN:"Tunisia",TO:"Tonga",TR:"Turkey",TT:"Trinidad and Tobago",TV:"Tuvalu",TW:"Taiwan",TZ:"Tanzania",UA:"Ukraine",UG:"Uganda",UM:"U.S. Minor Outlying Islands",US:"United States",UY:"Uruguay",UZ:"Uzbekistan",VA:"Vatican City",VC:"Saint Vincent and the Grenadines",VD:"North Vietnam",VE:"Venezuela",VG:"British Virgin Islands",VI:"U.S. Virgin Islands",VN:"Vietnam",VU:"Vanuatu",WF:"Wallis and Futuna",WK:"Wake Island",WS:"Samoa",YD:"People's Democratic Republic of Yemen",YE:"Yemen",YT:"Mayotte",ZA:"South Africa",ZM:"Zambia",ZW:"Zimbabwe",ZZ:"Unknown or Invalid Region" };

        var countriesArray = $.map(countries, function(value, key) {
          return {
            value: value,
            data: key
          };
        });

        // initialize autocomplete with custom appendTo
        $('#autocomplete-custom-append').autocomplete({
          lookup: countriesArray
        });
      });
    </script>
    <!-- /jQuery autocomplete -->

    <!-- Starrr -->
    <script>
      $(document).ready(function() {
        $(".stars").starrr();

        $('.stars-existing').starrr({
          rating: 4
        });

        $('.stars').on('starrr:change', function (e, value) {
          $('.stars-count').html(value);
        });

        $('.stars-existing').on('starrr:change', function (e, value) {
          $('.stars-count-existing').html(value);
        });
      });
    </script>
    <!-- /Starrr -->
  </body>
</html>
