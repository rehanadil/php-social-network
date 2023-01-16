<?php require_once('admincore.php');
$limit = 100;

if (isset($_GET['propagate'])) propagateLanguages();

if (!isset($_GET['id'])) header("Location: create_language.php");
$id = $escapeObj->stringEscape($_GET['id']);
$languageCheckQuery = $conn->query("SELECT * FROM " . DB_LANGUAGES . " WHERE type='$id'");
if ($languageCheckQuery->num_rows == 0) header("Location: create_language.php");
$total = $languageCheckQuery->num_rows;

if (isset($_POST['confirm_delete']))
{
  $conn->query("DELETE FROM " . DB_LANGUAGES . " WHERE type='$id'");
  unlink('../cache/languages/' . $id . '.php');
  header("Location: create_language.php");
}

if (isset($_GET['rebuild'])) rebuildLanguage($id, true);

if (isset($_GET['upload']))
{
  if (is_uploaded_file($_FILES['language_file']['tmp_name']))
  {
    $file = $_FILES['language_file'];
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (in_array($extension, array("json", "skl")))
    {
      $filepath = $id . ".json";
      if (move_uploaded_file($file['tmp_name'], $filepath))
      {
        $filedata = file_get_contents($filepath);
        $jsondata = json_decode($filedata, 1);

        if ($conn->query("DELETE FROM " . DB_LANGUAGES . " WHERE type='$id'"))
        {
          $sql = "INSERT INTO " . DB_LANGUAGES . " (type,keyword,text) VALUES ";
          $values = array();
          foreach ($jsondata as $key => $value)
          {
            $key = $escapeObj->stringEscape($key);
            $value = str_replace('&amp;', '&', $escapeObj->postEscape($value));
            $values[] = "('$id','$key','$value')";
          }
          $sql .= implode(',', $values);
          $create = $conn->query($sql);

          if ($create)
          {
            header("Location: ?id=$id");
          }
        }
      }
    }
  }
}

if (isset($_GET['export']))
{
  $exportArray = array();
  $exportQuery = $conn->query("SELECT * FROM " . DB_LANGUAGES . "
    WHERE type='$id'
    ORDER BY keyword ASC
    ");
  while ($exportLang = $exportQuery->fetch_array(MYSQLI_ASSOC))
  {
    $exportArray[$exportLang['keyword']] = $exportLang['text'];
  }

  $exportFile = $id . '.json';
  $export = file_put_contents($exportFile, json_encode($exportArray));

  if ($export)
  {
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary"); 
    header("Content-disposition: attachment; filename=\"" . basename($exportFile) . "\""); 
    readfile($exportFile);
    die();
  }
}

$languageQuery = $conn->query("SELECT * FROM " . DB_LANGUAGES . " WHERE type='$id' ORDER BY keyword ASC LIMIT $limit");
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

    <title>Edit <?php echo ucwords($id) ?> Language</title>

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
                    <h2>Edit <i><?php echo ucwords($id) ?></i> Language</h2>

                    <div class="floatRight">
                      <?php
                      if (isset($_GET['delete']))
                      {
                      ?>
                      <form method="post" action="?id=<?php echo $id ?>">
                        <button name="confirm_delete" class="languageButton deleteButton">Confirm Delete</button>
                        <a class="languageButton rebuildButton" href="?id=<?php echo $id ?>">Cancel</a>
                      </form>
                      <?php
                      }
                      else
                      {
                      ?>
                      <a class="languageButton propagateButton" href="?id=<?php echo $id ?>&propagate=1">Propagate</a>

                      <a class="languageButton rebuildButton" href="?id=<?php echo $id ?>&rebuild=1">Rebuild</a>

                      <a class="languageButton uploadButton">Upload</a>
                      <form id="uploadForm" method="post" action="?id=<?php echo $id ?>&upload=1" enctype="multipart/form-data" style="display:none;">
                        <input id="uploadInput" type="file" name="language_file" accept=".json,.skl">
                      </form>

                      <a class="languageButton exportButton" href="?id=<?php echo $id ?>&export=1">Export</a>

                      <a class="languageButton deleteButton" href="?id=<?php echo $id ?>&delete=1">Delete</a>
                      <?php
                      }
                      ?>
                    </div>

                    <div class="clearfix"></div>
                  </div>

                  <nav aria-label="Page navigation" align="left">
                      <ul class="pagination" id="paginationTop"></ul>
                  </nav>

                  <div class="x_content">
                    <br />
                    <form id="languageVariables" data-parsley-validate class="form-horizontal form-label-left"></form>
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
    <!-- bootstrap-progressbar -->
    <script src="vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- Autosize -->
    <script src="vendors/autosize/dist/autosize.min.js"></script>
    <!-- Esimakin twbs pagination -->
    <script src="vendors/esimakin-twbs-pagination/jquery.twbsPagination.min.js"></script>
    <!-- jQuery Form -->
    <script src="vendors/jquery-form/jquery.form.min.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <script>
    $('#paginationTop').twbsPagination({
      totalPages: <?php echo ceil($total/$limit); ?>,
      visiblePages: 7,
      onPageClick: function (event, page) {
        $.ajax({
          type: 'GET',
          url: 'requests.php',
          data: {
            a: "edit_lang_html",
            id: '<?php echo $id ?>',
            start: page,
            limit: <?php echo $limit ?>,
            delete: <?php echo (isset($_GET['delete'])) ? 1 : 0 ?>
          },
          dataType: "json",
          beforeSend: function()
          {
            $("#languageVariables").html("");
          },
          success: function(result)
          {
            if (result.status == 200) $("#languageVariables").html(result.html);
            $(document.body).scrollTop(0);
          }
        });
      }
    });
    </script>

    <!-- Upload -->
    <script>
    $(document).on("click", ".uploadButton", function(e){
      $('#uploadInput').click();
    });
    $(document).on("change", "#uploadInput", function(e){
      $('#uploadForm').submit();
    });
    </script>

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
    var typingTimer;

    function editLanguageKey(input)
    {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(function(){
          submitLanguageKey(input);
      }, 500);
    }
    function submitLanguageKey(input)
    {
      $.ajax({
        type: 'POST',
        url: 'requests.php',
        data: {
          a: "edit_language_key",
          key: input,
          value: $("textarea[name='" + input + "']").val(),
          language: '<?php echo $id ?>'
        },
        dataType: "json",
        beforeSend: function()
        {
          iconProgress($("#" + input + "Icon"));
        },
        success: function(result)
        {
          iconProgress($("#" + input + "Icon"));
          $("div." + input).find(".rebuildNotice").fadeIn('fast', function(){
            setTimeout(function(){
              $("div." + input).find(".rebuildNotice").fadeOut('fast');
            }, 10000);
          }).css("display","inline-block");
        }
      });
    }
    </script>
  </body>
</html>
