<?php require_once('admincore.php');
if (!isset($_GET['a'])) header("Location: index.php");
$a = $escapeObj->stringEscape($_GET['a']);
$addonUrl = '../addons/' . $a;
if (!file_exists($addonUrl)) header("Location: index.php");

if (isset($_POST['disable_addon'])
  && isset($_POST['addon']))
{
  if ($_POST['addon'] === $a)
  {
    unlink($addonUrl . '/enabled.html');
    $conn->close();
    die();
  }
}

if (isset($_POST['enable_addon'])
  && isset($_POST['addon']))
{
  if ($_POST['addon'] === $a)
  {
    file_put_contents($addonUrl . '/enabled.html', 1);
    $conn->close();
    die();
  }
}

$isSettings = (file_exists($addonUrl . '/settings.json')) ? true : false;
if ($isSettings) $sets = json_decode(file_get_contents($addonUrl . '/settings.json'), true);

if (isset($_POST['change_settings'])
  && isset($_POST['addon'])
  && $isSettings)
{
  if ($_POST['addon'] === $a)
  {
    unset($_POST['addon'], $_POST['change_settings']);
    $newsets = array();
    foreach ($_POST as $postIndex => $postValue)
    {
      if (array_key_exists($postIndex, $sets)) $newsets[$postIndex] = $postValue;
      foreach ($sets as $setIndex => $setValue)
      {
        if ($setValue['name'] === $postIndex) $newsets[$postIndex] = $postValue;
      }
    }
    
    if (count($newsets) == count($sets)) file_put_contents($addonUrl . '/data.json', json_encode($newsets));
  }
}

if ($isSettings) $dataset = json_decode(file_get_contents($addonUrl . '/data.json'), true);

include($addonUrl . '/info.php');
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

    <title>Addon | <?php echo $name ?></title>

    <link href="https://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet">
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

        <?php include($addonUrl . '/info.php'); ?>
        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title"></div>
            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel addonOverlay">
                  <div class="x_content">

                    <div class="addonContainer">
                      <div class="addonPreview">
                        <img class="preview" src="<?php echo $addonUrl ?>/preview.png" width="100%">
                        <div class="previewOverlay"></div>
                      </div>

                      <div class="addonInfo">
                        <div class="addonIcon">
                          <img class="icon" src="<?php echo $addonUrl ?>/icon.png">
                        </div>

                        <div class="addonName">
                          <strong><?php echo ucfirst($name) ?></strong>
                        </div>
                      </div>

                      <div id="addon_<?php echo $a ?>" class="addonButton">
                        <?php if (file_exists($addonUrl . '/enabled.html')) { ?>
                        <button class="green" onclick="disableAddon('<?php echo $a ?>');"><i class="fa fa-check"></i> Enabled</button>
                        <?php } else { ?>
                        <button class="blue" onclick="enableAddon('<?php echo $a ?>');"><i class="fa fa-circle-o"></i> Enable</button>
                        <?php } ?>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Addon Settings</h2>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                      <?php
                      if ($isSettings)
                      {
                      ?>
                      <form data-parsley-validate class="form-horizontal form-label-left" method="post" action="?a=<?php echo $a ?>">
                        <?php
                        foreach ($sets as $sIndex => $sInput)
                        {
                        ?>
                        <div class="form-group">
                          <label for="<?php echo $sInput['name'] ?>" class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $sIndex ?> <span class="required">*</span>
                          <br><small><?php echo $sInput['info'] ?></small>
                          </label>

                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <?php
                            if ($sInput['tag'] === "select")
                            {
                              if (count($sInput['options']) > 10)
                              {

                              }
                              else
                              {
                              ?>
                              <div class="btn-group" data-toggle="buttons">
                                <?php
                                foreach ($sInput['options'] as $optIndex => $optValue)
                                {
                                ?>
                                <label class="btn btn-default<?php if ($optIndex == $dataset[$sInput['name']]) echo ' active'; ?>" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                  <input type="radio" name="<?php echo $sInput['name'] ?>"<?php if ($optIndex == $dataset[$sInput['name']]) echo ' checked="yes"'; ?> value="<?php echo $optIndex ?>"> &nbsp; <?php echo $optValue ?> &nbsp;
                                </label>
                                <?php
                                }
                                ?>
                              </div>
                              <?php
                              }
                              ?>
                            <?php
                            }
                            else
                            {
                            ?>
                            <input name="<?php echo $sInput['name'] ?>" required="required" class="form-control col-md-7 col-xs-12" value="<?php echo $config['site_name'] ?>" placeholder="<?php echo $sIndex ?>" type="text">
                            <?php
                            }
                            ?>
                          </div>
                        </div>
                        <?php
                        }
                        ?>
                        <div class="ln_solid"></div>
                        
                        <div class="form-group">
                          <div class="col-md-6 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">Cancel</button>
                            <button id="send" type="submit" class="btn btn-success">Save Changes</button>
                          </div>
                        </div>
                        <input type="hidden" name="change_settings" value="1">
                        <input type="hidden" name="addon" value="<?php echo $a ?>">
                      </form>
                      <?php
                      } else {
                      ?>
                      <div class="bs-example" data-example-id="simple-jumbotron">
                        <div class="jumbotron" align="center">
                          <p>There are no settings or options to display</p>
                        </div>
                      </div>
                      <?php
                      }
                      ?>
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

    <script>
    function disableAddon(addon)
    {
      $.ajax({
        type: 'POST',
        url: '?a=' + addon,
        data: {
          disable_addon: true,
          addon: addon
        },
        dataType: "json",
        beforeSend: function()
        {
          //$("#addon_" + theme).find("button").removeClass("blue").addClass("green").html('<i class="fa fa-check"></i> Enabled');
          $("button.green").removeClass("green").addClass("blue").html('<i class="fa fa-circle-o"></i> Enable');
        }
      });
    }
    function enableAddon(addon)
    {
      $.ajax({
        type: 'POST',
        url: '?a=' + addon,
        data: {
          enable_addon: true,
          addon: addon
        },
        dataType: "json",
        beforeSend: function()
        {
          $("button.blue").removeClass("blue").addClass("green").html('<i class="fa fa-check"></i> Enabled');
        }
      });
    }
    </script>

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
