<?php require_once('admincore.php');

if (isset($_POST['create_app'], $_POST['app_name']))
{
  $appName = $escapeObj->stringEscape($_POST['app_name']);
  $appSecret = md5(generateKey() . time());
  $query = $conn->query("INSERT INTO " . DB_DEV_APPS . "
    (active,name,secret,time,timeline_id)
    VALUES
    (1,'$appName','$appSecret'," . time() . "," . $user['id'] . ")
  ");

  if ($query)
  {
    //cacheBannedFile();
    $entryId = $conn->insert_id;
    $data = array(
      "status" => 200,
      "html" => appsList(array(
        "id" => $entryId
      ))
    );

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($data);
    $conn->close();
    exit();
  }
}
elseif (isset($_POST['delete_app'], $_POST['app_id']))
{
  $appId = (int) $_POST['app_id'];
  $query = $conn->query("DELETE FROM " . DB_DEV_APPS . "
    WHERE id=$appId
  ");

  if ($query)
  {
    //cacheBannedFile();
    $data = array(
      "status" => 200
    );
    
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($data);
    $conn->close();
    exit();
  }
}

$totalAppsQuery = $conn->query("SELECT COUNT(id) AS countid FROM " . DB_DEV_APPS);
$totalAppsData = $totalAppsQuery->fetch_array(MYSQLI_ASSOC);
$totalApps = $totalAppsData['countid'];
$limit = 50;

if (isset($_GET['apps_list']))
{
  $data = array(
    "status" => 200,
    "html" => appsList(array(
      'limit' => (isset($_GET['limit'])) ? (int) $_GET['limit'] : 100,
      'start' => (isset($_GET['start'])) ? (int) $_GET['start'] : 0,
      'search' => (isset($_GET['search'])) ? $_GET['search'] : ""
    ))
  );

  header("Content-type: application/json; charset=utf-8");
  echo json_encode($data);
  $conn->close();
  exit();
}

function appsList($Array=array())
{
  $escapeObj = new \SocialKit\Escape();
  $id = (isset($Array['id'])) ? (int) $Array['id'] : 0;
  $limit = (isset($Array['limit'])) ? (int) $Array['limit'] : 100;
  $start = ((isset($Array['start'])) ? (int) $Array['start'] : 0);
  if ($start > 0) $start--;
  $start *= $limit;
  $search = (isset($Array['search'])) ? $escapeObj->stringEscape($Array['search']) : "";

  global $conn;
  $appsSql = "SELECT * FROM " . DB_DEV_APPS . " WHERE id>0";

  if ($id > 0) $appsSql .= " AND id=$id";

  if ($search !== "") $appsSql .= " AND name LIKE '%$search%'";

  $appsSql .= " ORDER BY id DESC LIMIT $start,$limit";
  $appsQuery = $conn->query($appsSql);
  $html = '';
  while ($app = $appsQuery->fetch_array(MYSQLI_ASSOC))
  {
    $timelineObj = new \SocialKit\User();
    $timelineObj->setId($app['timeline_id']);
    $timeline = $timelineObj->getRows();

    $html .= '<tr id="app_' . $app['id'] . '">
      <td>&bull;</td>

      <td><strong>' . $app['name'] . '</strong></td>

      <td>' . $app['id'] . '</td>

      <td>' . $app['secret'] . '</td>

      <td data-mobile="0">
        <strong>
          <a href="' . $timeline['url'] . '" target="_blank">' . $timeline['name'] . '</a>
        </strong>';

      if ($timeline['verified'] == 1) $html .= '<i class="verifiedBadge fa fa-check-circle"></i>';

      $html .= '</td>

      <td data-mobile="0">' . date('M j, Y', $app['time']) . '</td>

      <td>
        <a onclick="deleteApp(' . $app['id'] . ');" style="color:red;cursor:pointer"><i class="fa fa-times-circle"></i> Delete</a>
      </td>
    </tr>';
  }
    return $html;
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

    <title>Dev Apps | Admin Panel</title>

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
                    <h2>Developer Apps</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="floatLeft">
                    <nav aria-label="Page navigation" align="left">
                        <ul class="pagination" id="paginationTop"></ul>
                    </nav>
                  </div>

                  <div class="floatRight" style="margin-top: 18px;">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input type="text" class="form-control has-feedback-left" id="appSearchInput" placeholder="Search...">
                        <span class="fa fa-search form-control-feedback left appSearchIcon" aria-hidden="true"></span>
                      </div>
                  </div>

                  <div class="floatClear"></div>

                  <div class="x_content shortNotice">
                    <input id="create_app_input" type="text" placeholder="Write the name of your app...">
                    <button onclick="createApp();">Create New App</button>
                  </div>

                  <div class="x_content">
                    <table class="table table-hover appTable">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>App ID</th>
                          <th>Secret</th>
                          <th data-mobile="0">Created By</th>
                          <th data-mobile="0">Created On</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="appTableBody"></tbody>
                    </table>

                    <nav aria-label="Page navigation" align="right">
                        <ul class="pagination" id="paginationBottom"></ul>
                    </nav>
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
    <!-- Esimakin twbs pagination -->
    <script src="vendors/esimakin-twbs-pagination/jquery.twbsPagination.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <script>
    function createApp()
    {
      $.ajax({
        type: 'POST',
        url: '?',
        data: {
          create_app: 1,
          app_name: $("#create_app_input").val()
        },
        dataType: "json",
        beforeSend: function()
        {
          $("#create_app_input").val('');
        },
        success: function(result)
        {
          if (result.status == 200)
          {
            $("#appTableBody").prepend(result.html);
            $(document.body).scrollTop(0);
          }
        }
      });
    }

    function deleteApp(appId)
    {
      $.ajax({
        type: 'POST',
        url: '?',
        data: {
          delete_app: 1,
          app_id: appId
        },
        dataType: "json",
        beforeSend: function()
        {
          //
        },
        success: function(result)
        {
          if (result.status == 200)
          {
            $("#app_" + appId).remove();
          }
        }
      });
    }
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
    var typingTimer;
    var searchValue;
    var $pagination = $('#paginationTop, #paginationBottom');
    var defaultPagingOpts = {
        totalPages: <?php echo ceil($totalApps/$limit); ?>,
        visiblePages: 15,
        onPageClick: function (event, page) {
          $.ajax({
            type: 'GET',
            url: '?',
            data: {
              apps_list: 1,
              limit: <?php echo $limit ?>,
              start: page,
              search: $("#appSearchInput").val()
            },
            dataType: "json",
            beforeSend: function()
            {
              //
            },
            success: function(result)
            {
              if (result.status == 200) $("#appTableBody").html(result.html);
              $(document.body).scrollTop(0);
            }
          });
        }
    };
    
    $(function () {
        $pagination.twbsPagination(defaultPagingOpts);

        $(document).on("keyup", "#appSearchInput", function(){
          $("#appTableBody").html('');
          searchQuery = $(this).val();
          clearTimeout(typingTimer);
          typingTimer = setTimeout(function(){
              userSearch(searchQuery);
          }, 300);
        });

        $(document).on("keydown", "#appSearchInput", function(){
          clearTimeout(typingTimer);
        });

        function userSearch(s)
        {
          if (s !== searchValue)
          {
            searchValue = s;
            $pagination.twbsPagination('destroy');
            $pagination.twbsPagination($.extend({}, defaultPagingOpts));
          }
        }
    });
    </script>
  </body>
</html>
