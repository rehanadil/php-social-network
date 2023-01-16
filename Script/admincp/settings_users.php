<?php require_once('admincore.php');
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

    <title>Users Settings</title>

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
    <!-- Ion.RangeSlider -->
    <link href="vendors/normalize-css/normalize.css" rel="stylesheet">
    <link href="vendors/ion.rangeSlider/css/ion.rangeSlider.css" rel="stylesheet">
    <link href="vendors/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css" rel="stylesheet">
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
                    <h2>User Settings <small>Default settings for users</small></h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">
                      <div class="form-group">
                        <label for="followprivacy" class="control-label col-md-3 col-sm-3 col-xs-12">Default Profile Picture <span class="required">*</span> <br><small>250 x 250</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="kv-avatar-errors-1" class="center-block" style="width:800px;display:none"></div>

                          <form class="imageUploadForm" method="post" enctype="multipart/form-data" action="requests.php?a=default_female_avatar">
                            <input class="femaleavatar" name="avatar" type="file">

                            <i class="fa fa-camera"></i>

                            <div class="image">
                              <img class="femaleavatar" src="<?php echo $config['site_url'] . '/' . $config['user_default_female_avatar'] ?>">
                            </div>
                          </form>

                          <form class="imageUploadForm" method="post" enctype="multipart/form-data" action="requests.php?a=default_male_avatar">
                            <input class="maleavatar" name="avatar" type="file">

                            <i class="fa fa-camera"></i>

                            <div class="image">
                              <img class="maleavatar" src="<?php echo $config['site_url'] . '/' . $config['user_default_male_avatar'] ?>">
                            </div>
                          </form>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="followprivacy" class="control-label col-md-3 col-sm-3 col-xs-12">Default Cover Picture <span class="required">*</span> <br><small>920 x 276</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div id="kv-cover-errors-1" class="center-block" style="width:800px;display:none"></div>
                          
                          <form class="coverUploadForm" method="post" enctype="multipart/form-data" action="requests.php?a=default_cover">
                            <input class="cover" name="cover" type="file">

                            <i class="fa fa-camera"></i>

                            <div class="image">
                              <img class="cover" src="<?php echo $config['site_url'] . '/' . $config['user_default_cover'] ?>">
                            </div>
                          </form>
                        </div>
                      </div>
                      <?php
                      $friendsButton = "People Followed by User";
                      if ($config['friends'] == 1) $friendsButton = "Friends";
                      $cfq = $conn->query("EXPLAIN ". DB_USERS);
                      $cf = array();
                      while ($cff = mysqli_fetch_assoc($cfq)) $cf[$cff['Field']] = $cff['Default'];
                      ?>
                      <?php if ($config['friends'] == 0) { ?>
                      <div class="form-group">
                        <label for="confirm_followers" class="control-label col-md-3 col-sm-3 col-xs-12">Confirm Follow Requests <span class="required">*</span> <br><small>Do users need to accept/reject followers?</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($cf['confirm_followers'] == 1) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrConfirmFollowers" type="radio" name="confirm_followers"<?php if ($cf['confirm_followers'] == 1) echo ' checked="yes"'; ?> value="1" onchange="saveUserConfig('.usrConfirmFollowers', '#usrConfirmFollowersIcon', 1);"> &nbsp; Yes &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($cf['confirm_followers'] == 0) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrConfirmFollowers" type="radio" name="confirm_followers"<?php if ($cf['confirm_followers'] == 0) echo ' checked="yes"'; ?> value="0" onchange="saveUserConfig('.usrConfirmFollowers', '#usrConfirmFollowersIcon', 0);"> &nbsp; No &nbsp;
                            </label>
                          </div>

                          <i id="usrConfirmFollowersIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="follow_privacy" class="control-label col-md-3 col-sm-3 col-xs-12">Follow Privacy <span class="required">*</span> <br><small>Who can follow users?</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($cf['follow_privacy'] === "everyone") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrFollowPrivacy" type="radio" name="follow_privacy"<?php if ($cf['follow_privacy'] == "everyone") echo ' checked="yes"'; ?> value="everyone" onchange="saveUserConfig('.usrFollowPrivacy', '#usrFollowPrivacyIcon', 'everyone');"> &nbsp; Everyone &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($cf['follow_privacy'] === "following") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="follow_privacy"<?php if ($cf['follow_privacy'] == "following") echo ' checked="yes"'; ?> value="following" onchange="saveUserConfig('.usrFollowPrivacy', '#usrFollowPrivacyIcon', 'following');"> &nbsp; <?php echo $friendsButton; ?> &nbsp;
                            </label>
                          </div>

                          <i id="usrFollowPrivacyIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>
                      <?php } ?>

                      <div class="form-group">
                        <label for="message_privacy" class="control-label col-md-3 col-sm-3 col-xs-12">Message Privacy <span class="required">*</span> <br><small>Who can message users?</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($cf['message_privacy'] == "everyone") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrMessagePrivacy" type="radio" name="message_privacy"<?php if ($cf['message_privacy'] == "everyone") echo ' checked="yes"'; ?> value="everyone" onchange="saveUserConfig('.usrMessagePrivacy', '#usrMessagePrivacyIcon', 'everyone');"> &nbsp; Everyone &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($cf['message_privacy'] == "following") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input type="radio" name="message_privacy"<?php if ($cf['message_privacy'] == "following") echo ' checked="yes"'; ?> value="following" onchange="saveUserConfig('.usrMessagePrivacy', '#usrMessagePrivacyIcon', 'following');"> &nbsp; <?php echo $friendsButton; ?> &nbsp;
                            </label>
                          </div>

                          <i id="usrMessagePrivacyIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="timeline_post_privacy" class="control-label col-md-3 col-sm-3 col-xs-12">Timeline Post Privacy <span class="required">*</span> <br><small>Who can post on users timeline?</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($cf['timeline_post_privacy'] == "everyone") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrTimelinePostPrivacy" type="radio" name="timeline_post_privacy"<?php if ($cf['timeline_post_privacy'] == "everyone") echo ' checked="yes"'; ?> value="everyone" onchange="saveUserConfig('.usrTimelinePostPrivacy', '#usrTimelinePostPrivacyIcon', 'everyone');"> &nbsp; Everyone &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($cf['timeline_post_privacy'] == "following") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrTimelinePostPrivacy" type="radio" name="timeline_post_privacy"<?php if ($cf['timeline_post_privacy'] == "following") echo ' checked="yes"'; ?> value="following" onchange="saveUserConfig('.usrTimelinePostPrivacy', '#usrTimelinePostPrivacyIcon', 'following');"> &nbsp; <?php echo $friendsButton; ?> &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($cf['timeline_post_privacy'] == "none") echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrTimelinePostPrivacy" type="radio" name="timeline_post_privacy"<?php if ($cf['timeline_post_privacy'] == "none") echo ' checked="yes"'; ?> value="none" onchange="saveUserConfig('.usrTimelinePostPrivacy', '#usrTimelinePostPrivacyIcon', 'none');"> &nbsp; No One &nbsp;
                            </label>
                          </div>

                          <i id="usrTimelinePostPrivacyIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="user_allow_privacy_settings" class="control-label col-md-3 col-sm-3 col-xs-12">Allow Privacy Settings <span class="required">*</span> <br><small>Can users change their privacy settings?</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($config['user_allow_privacy_settings'] == 1) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrAllowPrivacy" type="radio" name="user_allow_privacy_settings"<?php if ($config['user_allow_privacy_settings'] == 1) echo ' checked="yes"'; ?> value="1" onchange="saveUserConfig('.usrAllowPrivacy', '#usrAllowPrivacyIcon', 1);"> &nbsp; Yes &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($config['user_allow_privacy_settings'] == 0) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="usrAllowPrivacy" type="radio" name="user_allow_privacy_settings"<?php if ($config['user_allow_privacy_settings'] == 0) echo ' checked="yes"'; ?> value="0" onchange="saveUserConfig('.usrAllowPrivacy', '#usrAllowPrivacyIcon', 0);"> &nbsp; No &nbsp;
                            </label>
                          </div>

                          <i id="usrAllowPrivacyIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

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
    <!-- Ion.RangeSlider -->
    <script src="vendors/ion.rangeSlider/js/ion.rangeSlider.min.js"></script>
    <!-- Fileinput -->
    <script src="js/jquery.form.min.js"></script>
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

    <script>
    $(document).on("change", "input[type=file]", function(){
      fileInput = $(this);
      fileForm = fileInput.parent('form');
      fileForm.ajaxSubmit({
        success: function(response)
        {
          if (response.status === 200)
          {
            $("img." + fileInput.attr("class")).attr("src", response.src);
          }
        }
      });
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

    <script>
    function saveUserConfig(input, icon, val)
    {
      if (typeof(val) === "undefined") val = $(input).val();
      name = $(input).attr("name");
      $.ajax({
        type: 'POST',
        url: 'requests.php',
        data: {
          a: "user_config",
          name: name,
          value: val
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
