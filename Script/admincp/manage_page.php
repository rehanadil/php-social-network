<?php require_once('admincore.php');
if (empty($_GET['id'])) header("Location: manage_pages.php");
$pgId = (int) $_GET['id'];
$pgObj = new \SocialKit\User();
$pgObj->setId($pgId);
$pg = $pgObj->getRows();

if (isset($_POST['delete_page'])
  && !empty($_POST['page_id']))
{
  $queries = array();
  $queries[] = "DELETE FROM " . DB_POSTS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_POSTS . " WHERE recipient_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_MEDIA . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_FOLLOWERS . " WHERE following_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_PAGE_ADMINS . " WHERE page_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_ACCOUNTS . " WHERE id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_PAGES . " WHERE id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_ACCOUNTS . " WHERE id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_ANNOUNCEMENT_VIEWS . " WHERE account_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_BLOCKED_USERS . " WHERE blocker_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_BLOCKED_USERS . " WHERE blocked_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_COMMENTS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_COMMENTLIKES . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_EVENTS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_EVENT_INVITES . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_FOLLOWERS . " WHERE follower_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_FOLLOWERS . " WHERE following_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_LOGIN_SESSIONS . " WHERE user_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_MEDIA . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_MESSAGES . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_MESSAGES . " WHERE recipient_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_NOTIFICATIONS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_PAGE_ADMINS . " WHERE admin_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_PINNEDPOSTS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_PLAN_SALES . " WHERE user_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_POSTS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_POSTS . " WHERE recipient_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_POSTFOLLOWS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_POSTLIKES . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_POSTSHARES . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_REPORTS . " WHERE reporter_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_SAVEDPOSTS . " WHERE timeline_id=" . $pgId;
  $queries[] = "DELETE FROM " . DB_USER_COLORS . " WHERE user1=" . $pgId;
  $queries[] = "DELETE FROM " . DB_USER_COLORS . " WHERE user2=" . $pgId;

  foreach ($queries as $query) {
    $conn->query($query);
  }

  header("Location: manage_pages.php");
}

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

    <title>Edit Page &raquo; <?php echo $pg['name'] ?> | Admin Panel</title>

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
    <!-- bootstrap-daterangepicker -->
    <link href="vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="build/css/custom.css" rel="stylesheet">
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
                    <h2>Edit Page: <?php echo $pg['name'] ?></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="form-group">
                        <label for="verified" class="control-label col-md-3 col-sm-3 col-xs-12">Verified <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($pg['verified'] == 1) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="verifiedInput" type="radio" name="verified"<?php if ($pg['verified'] == 1) echo ' checked="yes"'; ?> value="1" onchange="savePageInfo('.verifiedInput', '#pgVerifiedIcon', 1);"> &nbsp; On &nbsp;
                            </label>
                            <label class="btn btn-default<?php if ($pg['verified'] == 0) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="verifiedInput" type="radio" name="verified"<?php if ($pg['verified'] == 0) echo ' checked="yes"'; ?> value="0" onchange="savePageInfo('.verifiedInput', '#pgVerifiedIcon', 0);"> &nbsp; Off &nbsp;
                            </label>
                          </div>
                          <i id="pgVerifiedIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgName" name="name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['name'] ?>" type="text" onkeyup="savePageInfo('#pgName', '#pgNameIcon');">
                          <i id="pgNameIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="username" class="control-label col-md-3 col-sm-3 col-xs-12">Username <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgUsername" name="username" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['username'] ?>" type="text" onkeyup="savePageInfo('#pgUsername', '#pgUsernameIcon');">
                          <i id="pgUsernameIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="category_id" class="control-label col-md-3 col-sm-3 col-xs-12">Category <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="pgCategoryId" name="category_id" required="required" class="select2_single form-control" tabindex="-1" onchange="savePageInfo('#pgCategoryId', '#pgCategoryIdIcon');">
                            <?php
                            $mainCategoriesQuery = $conn->query("SELECT id,name FROM ". DB_PAGE_CATEGORIES ." WHERE category_id=0 AND active=1");
                            while ($mainCategory = $mainCategoriesQuery->fetch_array(MYSQLI_ASSOC)){
                            ?>
                            <optgroup label="<?php echo $lang[$mainCategory['name']]; ?>">
                                <?php
                                $childCategoriesQuery = $conn->query("SELECT id,name FROM ". DB_PAGE_CATEGORIES ." WHERE category_id=". $mainCategory['id'] ." AND active=1");
                                while ($childCategory = $childCategoriesQuery->fetch_array(MYSQLI_ASSOC)){
                                ?>
                                <option value="<?php echo $childCategory['id']; ?>"<?php if ($pg['category_id'] == $childCategory['id']) echo ' selected'; ?>>
                                    <?php echo $lang[$childCategory['name']]; ?>
                                </option>
                                <?php } ?>
                            </optgroup>
                            <?php } ?>
                          </select>
                          <i id="pgCategoryIdIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="email" class="control-label col-md-3 col-sm-3 col-xs-12">Email <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgEmail" name="email" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['email'] ?>" type="text" onkeyup="savePageInfo('#pgEmail', '#pgEmailIcon');">
                          <i id="pgEmailIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="about" class="control-label col-md-3 col-sm-3 col-xs-12">About <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea id="pgAbout" name="about" required="required" class="form-control col-md-7 col-xs-12" onkeyup="savePageInfo('#pgAbout', '#pgAboutIcon');" style="resize:vertical;min-height:120px"><?php echo $pg['about'] ?></textarea>
                          <i id="pgAboutIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="address" class="control-label col-md-3 col-sm-3 col-xs-12">Address <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgAddress" name="address" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['address'] ?>" type="text" onkeyup="savePageInfo('#pgAddress', '#pgAddressIcon');">
                          <i id="pgAddressIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="awards" class="control-label col-md-3 col-sm-3 col-xs-12">Awards <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgAwards" name="awards" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['awards'] ?>" type="text" onkeyup="savePageInfo('#pgAwards', '#pgAwardsIcon');">
                          <i id="pgAwardsIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="phone" class="control-label col-md-3 col-sm-3 col-xs-12">Phone <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgPhone" name="phone" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['phone'] ?>" type="text" onkeyup="savePageInfo('#pgPhone', '#pgPhoneIcon');">
                          <i id="pgPhoneIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="products" class="control-label col-md-3 col-sm-3 col-xs-12">Products <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgProducts" name="products" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['products'] ?>" type="text" onkeyup="savePageInfo('#pgProducts', '#pgProductsIcon');">
                          <i id="pgProductsIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="website" class="control-label col-md-3 col-sm-3 col-xs-12">Website <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="pgWebsite" name="website" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $pg['website'] ?>" type="text" onkeyup="savePageInfo('#pgWebsite', '#pgWebsiteIcon');">
                          <i id="pgWebsiteIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="message_privacy" class="control-label col-md-3 col-sm-3 col-xs-12">Message Privacy <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($pg['message_privacy'] === "everyone") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="pgMessagePrivacy" type="radio" name="message_privacy"<?php if ($pg['message_privacy'] === "everyone") echo ' checked="yes"'; ?> value="everyone" onchange="savePageInfo('.pgMessagePrivacy', '#pgMessagePrivacyIcon', 'everyone');"> &nbsp; Everyone &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($pg['message_privacy'] === "following") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="pgMessagePrivacy" type="radio" name="message_privacy"<?php if ($pg['message_privacy'] === "following") echo ' checked="yes"'; ?> value="following" onchange="savePageInfo('.pgMessagePrivacy', '#pgMessagePrivacyIcon', 'following');"> &nbsp; Following &nbsp;
                            </label>
                          </div>
                          <i id="pgMessagePrivacyIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="timeline_post_privacy" class="control-label col-md-3 col-sm-3 col-xs-12">Timeline Post Privacy <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($pg['timeline_post_privacy'] === "everyone") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="pgTimelinePostPrivacy" type="radio" name="timeline_post_privacy"<?php if ($pg['timeline_post_privacy'] === "everyone") echo ' checked="yes"'; ?> value="everyone" onchange="savePageInfo('.pgTimelinePostPrivacy', '#pgTimelinePostPrivacyIcon', 'everyone');"> &nbsp; Everyone &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($pg['timeline_post_privacy'] === "following") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="pgTimelinePostPrivacy" type="radio" name="timeline_post_privacy"<?php if ($pg['timeline_post_privacy'] === "following") echo ' checked="yes"'; ?> value="following" onchange="savePageInfo('.pgTimelinePostPrivacy', '#pgTimelinePostPrivacyIcon', 'following');"> &nbsp; Following &nbsp;
                            </label>
                          </div>
                          <i id="pgTimelinePostPrivacyIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="ln_solid"></div>
                      <form class="form-group" method="post" action="?id=<?php echo $pg['id'] ?>">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <input type="hidden" name="page_id" value="<?php echo $pg['id'] ?>">
                          <button name="delete_page" type="submit" class="btn btn-danger">Delete Page</button>
                        </div>
                      </form>

                    </div>
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

    <!-- bootstrap-daterangepicker -->
    <script>
      $(document).ready(function() {
        $('#pgBirthday').daterangepicker({
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
        var countries = { AD:"Andorra",A2:"Andorra Test",AE:"United Arab Emirates",AF:"Afghanistan",AG:"Antigua and Barbuda",AI:"Anguilla",AL:"Albania",AM:"Armenia",AN:"Netherlands Antilles",AO:"Angola",AQ:"Antarctica",AR:"Argentina",AS:"American Samoa",AT:"Austria",AU:"Australia",AW:"Aruba",AX:"??land Islands",AZ:"Azerbaijan",BA:"Bosnia and Herzegovina",BB:"Barbados",BD:"Bangladesh",BE:"Belgium",BF:"Burkina Faso",BG:"Bulgaria",BH:"Bahrain",BI:"Burundi",BJ:"Benin",BL:"Saint Barth??lemy",BM:"Bermuda",BN:"Brunei",BO:"Bolivia",BQ:"British Antarctic Territory",BR:"Brazil",BS:"Bahamas",BT:"Bhutan",BV:"Bouvet Island",BW:"Botswana",BY:"Belarus",BZ:"Belize",CA:"Canada",CC:"Cocos [Keeling] Islands",CD:"Congo - Kinshasa",CF:"Central African Republic",CG:"Congo - Brazzaville",CH:"Switzerland",CI:"C??te d???Ivoire",CK:"Cook Islands",CL:"Chile",CM:"Cameroon",CN:"China",CO:"Colombia",CR:"Costa Rica",CS:"Serbia and Montenegro",CT:"Canton and Enderbury Islands",CU:"Cuba",CV:"Cape Verde",CX:"Christmas Island",CY:"Cyprus",CZ:"Czech Republic",DD:"East Germany",DE:"Germany",DJ:"Djibouti",DK:"Denmark",DM:"Dominica",DO:"Dominican Republic",DZ:"Algeria",EC:"Ecuador",EE:"Estonia",EG:"Egypt",EH:"Western Sahara",ER:"Eritrea",ES:"Spain",ET:"Ethiopia",FI:"Finland",FJ:"Fiji",FK:"Falkland Islands",FM:"Micronesia",FO:"Faroe Islands",FQ:"French Southern and Antarctic Territories",FR:"France",FX:"Metropolitan France",GA:"Gabon",GB:"United Kingdom",GD:"Grenada",GE:"Georgia",GF:"French Guiana",GG:"Guernsey",GH:"Ghana",GI:"Gibraltar",GL:"Greenland",GM:"Gambia",GN:"Guinea",GP:"Guadeloupe",GQ:"Equatorial Guinea",GR:"Greece",GS:"South Georgia and the South Sandwich Islands",GT:"Guatemala",GU:"Guam",GW:"Guinea-Bissau",GY:"Guyana",HK:"Hong Kong SAR China",HM:"Heard Island and McDonald Islands",HN:"Honduras",HR:"Croatia",HT:"Haiti",HU:"Hungary",ID:"Indonesia",IE:"Ireland",IL:"Israel",IM:"Isle of Man",IN:"India",IO:"British Indian Ocean Territory",IQ:"Iraq",IR:"Iran",IS:"Iceland",IT:"Italy",JE:"Jersey",JM:"Jamaica",JO:"Jordan",JP:"Japan",JT:"Johnston Island",KE:"Kenya",KG:"Kyrgyzstan",KH:"Cambodia",KI:"Kiribati",KM:"Comoros",KN:"Saint Kitts and Nevis",KP:"North Korea",KR:"South Korea",KW:"Kuwait",KY:"Cayman Islands",KZ:"Kazakhstan",LA:"Laos",LB:"Lebanon",LC:"Saint Lucia",LI:"Liechtenstein",LK:"Sri Lanka",LR:"Liberia",LS:"Lesotho",LT:"Lithuania",LU:"Luxembourg",LV:"Latvia",LY:"Libya",MA:"Morocco",MC:"Monaco",MD:"Moldova",ME:"Montenegro",MF:"Saint Martin",MG:"Madagascar",MH:"Marshall Islands",MI:"Midway Islands",MK:"Macedonia",ML:"Mali",MM:"Myanmar [Burma]",MN:"Mongolia",MO:"Macau SAR China",MP:"Northern Mariana Islands",MQ:"Martinique",MR:"Mauritania",MS:"Montserrat",MT:"Malta",MU:"Mauritius",MV:"Maldives",MW:"Malawi",MX:"Mexico",MY:"Malaysia",MZ:"Mozambique",NA:"Namibia",NC:"New Caledonia",NE:"Niger",NF:"Norfolk Island",NG:"Nigeria",NI:"Nicaragua",NL:"Netherlands",NO:"Norway",NP:"Nepal",NQ:"Dronning Maud Land",NR:"Nauru",NT:"Neutral Zone",NU:"Niue",NZ:"New Zealand",OM:"Oman",PA:"Panama",PC:"Pacific Islands Trust Territory",PE:"Peru",PF:"French Polynesia",PG:"Papua New Guinea",PH:"Philippines",PK:"Pakistan",PL:"Poland",PM:"Saint Pierre and Miquelon",PN:"Pitcairn Islands",PR:"Puerto Rico",PS:"Palestinian Territories",PT:"Portugal",PU:"U.S. Miscellaneous Pacific Islands",PW:"Palau",PY:"Paraguay",PZ:"Panama Canal Zone",QA:"Qatar",RE:"R??union",RO:"Romania",RS:"Serbia",RU:"Russia",RW:"Rwanda",SA:"Saudi Arabia",SB:"Solomon Islands",SC:"Seychelles",SD:"Sudan",SE:"Sweden",SG:"Singapore",SH:"Saint Helena",SI:"Slovenia",SJ:"Svalbard and Jan Mayen",SK:"Slovakia",SL:"Sierra Leone",SM:"San Marino",SN:"Senegal",SO:"Somalia",SR:"Suriname",ST:"S??o Tom?? and Pr??ncipe",SU:"Union of Soviet Socialist Republics",SV:"El Salvador",SY:"Syria",SZ:"Swaziland",TC:"Turks and Caicos Islands",TD:"Chad",TF:"French Southern Territories",TG:"Togo",TH:"Thailand",TJ:"Tajikistan",TK:"Tokelau",TL:"Timor-Leste",TM:"Turkmenistan",TN:"Tunisia",TO:"Tonga",TR:"Turkey",TT:"Trinidad and Tobago",TV:"Tuvalu",TW:"Taiwan",TZ:"Tanzania",UA:"Ukraine",UG:"Uganda",UM:"U.S. Minor Outlying Islands",US:"United States",UY:"Uruguay",UZ:"Uzbekistan",VA:"Vatican City",VC:"Saint Vincent and the Grenadines",VD:"North Vietnam",VE:"Venezuela",VG:"British Virgin Islands",VI:"U.S. Virgin Islands",VN:"Vietnam",VU:"Vanuatu",WF:"Wallis and Futuna",WK:"Wake Island",WS:"Samoa",YD:"People's Democratic Republic of Yemen",YE:"Yemen",YT:"Mayotte",ZA:"South Africa",ZM:"Zambia",ZW:"Zimbabwe",ZZ:"Unknown or Invalid Region" };

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

    <script>
    function savePageInfo(input, icon, val)
    {
      if (typeof(val) === "undefined") val = $(input).val();
      name = $(input).attr("name");
      $.ajax({
        type: 'POST',
        url: 'requests.php',
        data: {
          a: "page_info",
          name: name,
          value: val,
          page_id: <?php echo $pgId ?>
        },
        dataType: "json",
        beforeSend: function()
        {
          iconProgress($(icon));
        },
        success: function(result)
        {
          iconProgress($(icon));
        }
      });
    }
    </script>
  </body>
</html>
