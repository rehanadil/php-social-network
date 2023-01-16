<?php require_once('admincore.php');

if (isset($_FILES['import_keywords']))
{
  if (is_uploaded_file($_FILES['import_keywords']['tmp_name']))
  {
    $file = $_FILES['import_keywords'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filepath = md5(time()) . '.json';
    if ($file['size'] > 0
      && in_array($extension, array("skl", "json")))
    {
      if (move_uploaded_file($file['tmp_name'], $filepath))
      {
        $sql = "INSERT INTO " . DB_LANGUAGES . " (type,keyword,text) VALUES ";
        $valuesSql = array();
        $importData = file_get_contents($filepath);
        $importArray = json_decode($importData, true);
        $languagesQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES);
        while ($language = $languagesQuery->fetch_array(MYSQLI_ASSOC))
        {
          $lang = $language['type'];
          foreach ($importArray as $key => $value)
          {
            $key = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9_]+/i', '', $escapeObj->stringEscape($key))));
            $value = str_replace('&amp;', '&', $escapeObj->postEscape($value));
            $isKeywordExistQuery = $conn->query("SELECT id FROM " . DB_LANGUAGES . " WHERE type='$lang' AND keyword='$key'");
            if ($isKeywordExistQuery->num_rows == 1)
              $conn->query("UPDATE " . DB_LANGUAGES . " SET text='$value' WHERE type='$lang' AND keyword='$key'");
            else
              $valuesSql[] = "('$lang','$key','$value')";
          }
        }
        $sql .= implode(',', $valuesSql);
        $import = $conn->query($sql);
        unlink($filepath);
        header("Location: edit_language.php?id=$lang&imported=1#$key");
      }
    }
  }
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

    <title>Import Keywords</title>

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
              <form id="importKeywords" class="col-md-12 col-sm-12 col-xs-12" action="?" method="post" enctype="multipart/form-data">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Import Keywords</h2>

                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content shortNotice">
                    Make sure to <strong>Rebuild</strong> all of the languages after you import the keywords.
                  </div>

                  <div class="x_content">
                    <br />
                    <div id="demo-form2" data-parsley-validate class="form-horizontal form-label-left">

                      <div class="form-group">
                        <label for="text" class="control-label col-md-3 col-sm-3 col-xs-12">Upload Language File <span class="required">*</span>
                          <br><small>(.skl)</small>
                        </label>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <input class="form-control col-md-7 col-xs-12" type="file" name="import_keywords" accept=".skl">
                        </div>
                      </div>

                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                          <button class="createLanguageButton">Import</button>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
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
