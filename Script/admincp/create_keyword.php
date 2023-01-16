<?php require_once('admincore.php');

if (isset($_POST['create_new_keyword'])
  && isset($_POST['keyword'])
  && isset($_POST['values']))
{
  $gotoLang = "";
  $keyword = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9_]+/i', '', $escapeObj->stringEscape($_POST['keyword']))));
  $values = $_POST['values'];
  if (is_array($values))
  {
    $sql = "INSERT INTO " . DB_LANGUAGES . " (type,keyword,text) VALUES ";
    $valuesSql = array();
    foreach ($values as $lang => $value)
    {
      $lang = $escapeObj->stringEscape($lang);
      $value = str_replace('&amp;', '&', $escapeObj->postEscape($value));
      $isExistQuery = $conn->query("SELECT id FROM " . DB_LANGUAGES . " WHERE type='$lang' LIMIT 1");
      if ($isExistQuery->num_rows == 1)
      {
        $valuesSql[] = "('$lang','$keyword','$value')";
        if (empty($gotoLang)) $gotoLang = $lang;
      }
    }
    $sql .= implode(',', $valuesSql);
    $create = $conn->query($sql);
    if ($create) header("Location: edit_language.php?id=$gotoLang#$keyword");
  }
}

$languagesQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES);
if ($languagesQuery->num_rows == 0) header("Location: create_language.php");
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

    <title>Add New Keyword</title>

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
              <form id="createNewLanguage" class="col-md-12 col-sm-12 col-xs-12" action="?" method="post">
                <div class="x_panel">
                  <div class="x_title">
                    <div class="floatLeft">
                      <input class="languageName" type="text" name="keyword" placeholder="Name of your keyword...">
                    </div>

                    <div class="floatRight">
                      <button class="createLanguageButton">Create</button>
                    </div>

                    <div class="clearFloat"></div>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                      <?php
                      while ($language = $languagesQuery->fetch_array(MYSQLI_ASSOC))
                      {
                        $name = ucwords(str_replace('_', ' ', $language['type']));
                      ?>
                      <div class="form-group">
                        <label for="text" class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $name ?> <span class="required">*</span>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12 <?php echo $language['type'] ?>">
                          <textarea id="<?php echo $language['type'] ?>" name="values[<?php echo $language['type'] ?>]" required="required" placeholder="Type your <?php echo $name ?> translation here" class="form-control col-md-7 col-xs-12" style="resize:vertical"></textarea>
                        </div>
                      </div>
                      <?php
                      }
                      ?>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button class="createLanguageButton">Create</button>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <input type="hidden" name="create_new_keyword" value="1">
              </form>
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
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- Autosize -->
    <script src="vendors/autosize/dist/autosize.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <!-- Autosize -->
    <script>
      $(document).ready(function() {
        $("textarea").each(function(){
          autosize($(this));
        })
      });
    </script>
    <!-- /Autosize -->
  </body>
</html>
