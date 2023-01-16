<?php require_once('admincore.php');
$limit = 200;

if (isset($_POST['create_new_language'])
  && isset($_POST['name'])
  && isset($_POST['keywords']))
{
  $name = $escapeObj->stringEscape($_POST['name']);
  $keywords = $_POST['keywords'];
  $lang = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9_]+/i', '', $name)));
  if (is_array($keywords))
  {
    $isExistQuery = $conn->query("SELECT id FROM " . DB_LANGUAGES . " WHERE type='$lang' LIMIT 1");
    if ($isExistQuery->num_rows == 0)
    {
      $sql = "INSERT INTO " . DB_LANGUAGES . " (type,keyword,text) VALUES ";
      $values = array();
      foreach ($keywords as $key => $value)
      {
        $key = $escapeObj->stringEscape($key);
        $value = str_replace('&amp;', '&', $escapeObj->postEscape($value));
        $values[] = "('$lang','$key','$value')";
      }
      $sql .= implode(',', $values);
      $create = $conn->query($sql);
      if ($create) header("Location: edit_language.php?id=$lang");
    }
  }
}

$abstractLanguageQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES . " LIMIT 1");
if ($abstractLanguageQuery->num_rows != 1) header("Location: index.php");
$abstractLanguageData = $abstractLanguageQuery->fetch_array(MYSQLI_ASSOC);
$abstractLanguage = $abstractLanguageData['type'];

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

    <title>Add New Language</title>

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
                      <input class="languageName" type="text" name="name" placeholder="Name of your language...">
                    </div>

                    <div class="floatRight">
                      <button class="createLanguageButton" disabled>Create</button>
                    </div>

                    <div class="clearFloat"></div>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <div id="languageVariables" data-parsley-validate class="form-horizontal form-label-left"></div>
                  </div>
                </div>
                <input type="hidden" name="create_new_language" value="1">
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

    <script>
    var start = 1;
    function getVariables()
    {
      $.ajax({
        type: 'GET',
        url: 'requests.php',
        data: {
          a: "create_lang_html",
          start: start,
          limit: <?php echo $limit ?>
        },
        dataType: "json",
        success: function(result)
        {
          start++;
          if (result.status == 200)
          {
            $("#languageVariables").append(result.html);
            getVariables();
          }
          else
          {
            $(".createLanguageButton").removeAttr("disabled");
          }
        }
      });
    }
    $(function(){
      getVariables();
    });
    </script>
  </body>
</html>
