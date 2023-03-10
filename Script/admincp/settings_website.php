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

    <title>Website Settings</title>

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
                    <h2>Website Settings</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <form id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="form-group">
                        <label for="site_name" class="control-label col-md-3 col-sm-3 col-xs-12">Site Name <span class="required">*</span>
                        <br><small>Name of your website</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="siteName" name="site_name" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $config['site_name'] ?>" placeholder="Name of your website" type="text" onkeyup="saveSiteConfig('#siteName', '#siteNameIcon');">

                          <i id="siteNameIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="site_title" class="control-label col-md-3 col-sm-3 col-xs-12">Site Title <span class="required">*</span>
                        <br><small>Title of your website</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="siteTitle" name="site_title" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $config['site_title'] ?>" placeholder="Title of your website" type="text" onkeyup="saveSiteConfig('#siteTitle', '#siteTitleIcon');">

                          <i id="siteTitleIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="site_keywords" class="control-label col-md-3 col-sm-3 col-xs-12">Site Keywords <span class="required">*</span>
                        <br><small>SEO Keywords (e.g. social, messaging, people)</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="siteKeywords" name="site_keywords" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $config['site_keywords'] ?>" placeholder="Keywords for your website SEO" type="text" onkeyup="saveSiteConfig('#siteKeywords', '#siteKeywordsIcon');">

                          <i id="siteKeywordsIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="site_description" class="control-label col-md-3 col-sm-3 col-xs-12">Site Description <span class="required">*</span>
                        <br><small>Description for your website (SEO)</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea id="siteDescription" name="site_description" required="required" class="form-control col-md-7 col-xs-12" placeholder="Description for your website" onkeyup="saveSiteConfig('#siteDescription', '#siteDescriptionIcon');" style="resize:vertical;min-height:60px"><?php echo $config['site_description'] ?></textarea>

                          <i id="siteDescriptionIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="google_analytics" class="control-label col-md-3 col-sm-3 col-xs-12">Google Analytics Code <span class="required">*</span>
                        <br><small>Your Google Analytics Code</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <textarea id="googleAnalytics" name="google_analytics" required="required" class="form-control col-md-7 col-xs-12" placeholder="Your Google Analytics Code" onkeyup="saveSiteConfig('#googleAnalytics', '#googleAnalyticsIcon');" style="resize:vertical;min-height:60px"><?php echo $config['google_analytics'] ?></textarea>

                          <i id="googleAnalyticsIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="email" class="control-label col-md-3 col-sm-3 col-xs-12">Site Email <span class="required">*</span>
                        <br><small>Email address of your website.<br>All emails to your users will be sent from this email</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input id="siteEmail" name="email" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $config['email'] ?>" placeholder="Email address of your website. All emails to your users will be send from this email" type="text" onkeyup="saveSiteConfig('#siteEmail', '#siteEmailIcon');">

                          <i id="siteEmailIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="chat" class="control-label col-md-3 col-sm-3 col-xs-12">Chat <span class="required">*</span>
                        <br><small>Turn Live Chat On/Off for your website</small>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($config['chat'] == 1) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="siteChat" type="radio" name="chat"<?php if ($config['chat'] == 1) echo ' checked="yes"'; ?> value="1" onchange="saveSiteConfig('.siteChat', '#siteChatIcon', 1);"> &nbsp; On &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($config['chat'] == 0) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="siteChat" type="radio" name="chat"<?php if ($config['chat'] == 0) echo ' checked="yes"'; ?> value="0" onchange="saveSiteConfig('.siteChat', '#siteChatIcon', 0);"> &nbsp; Off &nbsp;
                            </label>
                          </div>

                          <i id="siteChatIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="language" class="control-label col-md-3 col-sm-3 col-xs-12">Default Language <span class="required">*</span>
                        <br><small>Default language for your website</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <select id="siteLanguage" name="language" required="required" class="select2_single form-control" tabindex="-1" onchange="saveSiteConfig('#siteLanguage', '#siteLanguageIcon');">
                            <?php
                            $langQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES);
                            while ($langFetch = $langQuery->fetch_array(MYSQLI_ASSOC))
                            {
                                $language = $langFetch['type'];
                                $language = str_replace('../languages/', '', $language);
                                $language = preg_replace('/([A-Za-z]+)\.php/i', '$1', $language);
                            ?>
                            <option value="<?php echo $language; ?>"<?php if ($language == $config['language']) echo ' selected'; ?>><?php echo ucfirst($language); ?></option>
                            <?php
                            }
                            ?>
                          </select>

                          <i id="siteLanguageIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="smooth_links" class="control-label col-md-3 col-sm-3 col-xs-12">Smooth Links <span class="required">*</span>
                        <br><small>Turn Smooth Links On/Off for your website<br>For example: <?php echo $config['site_url'] ?>/home</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default<?php if ($config['smooth_links'] == 1) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="smoothLinks" type="radio" name="smooth_links"<?php if ($config['smooth_links'] == 1) echo ' checked="yes"'; ?> value="1" onchange="saveSiteConfig('.smoothLinks', '#siteSmoothLinksIcon', 1);"> &nbsp; On &nbsp;
                            </label>

                            <label class="btn btn-default<?php if ($config['smooth_links'] == 0) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                              <input class="smoothLinks" type="radio" name="smooth_links"<?php if ($config['smooth_links'] == 0) echo ' checked="yes"'; ?> value="0" onchange="saveSiteConfig('.smoothLinks', '#siteSmoothLinksIcon', 0);"> &nbsp; Off &nbsp;
                            </label>
                          </div>

                          <i id="siteSmoothLinksIcon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>
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
    function saveSiteConfig(input, icon, val)
    {
      if (typeof(val) === "undefined") val = $(input).val();
      name = $(input).attr("name");
      $.ajax({
        type: 'POST',
        url: 'requests.php',
        data: {
          a: "site_info",
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
