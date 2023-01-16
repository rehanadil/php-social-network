<?php require_once('admincore.php');

$bannedHtml = '<span class="bannedStatus">Banned</span>';
$inactiveHtml = 'Inactive';

if (isset($_POST['ban_ip'], $_POST['ip_addr']))
{
  $ipAddress = $escapeObj->stringEscape($_POST['ip_addr']);

  if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/i', $ipAddress))
  {
    $query = $conn->query("INSERT INTO " . DB_BANNED_IPS . "
      (active,admin_id,ip)
      VALUES
      (1," . $user['id'] . ",'$ipAddress')
    ");

    if ($query)
    {
      cacheBannedFile();
      $entryId = $conn->insert_id;
      $data = array(
        "status" => 200,
        "html" => ipsList(array(
          "id" => $entryId
        ))
      );

      header("Content-type: application/json; charset=utf-8");
      echo json_encode($data);
      $conn->close();
      exit();
    }
  }
}

if (isset($_POST['ip_id']))
{
  $ipId = (int) $_POST['ip_id'];

  if (isset($_POST['activate_ip']))
  {
    $query = $conn->query("UPDATE " . DB_BANNED_IPS . "
      SET active=1
      WHERE id=$ipId
    ");

    if ($query)
    {
      cacheBannedFile();
      $data = array(
        "status" => 200,
        "status_html" => $bannedHtml,
        "status_button_html" => inactivateButton($ipId)
      );
      
      header("Content-type: application/json; charset=utf-8");
      echo json_encode($data);
      $conn->close();
      exit();
    }
  }
  elseif (isset($_POST['inactivate_ip']))
  {
    $query = $conn->query("UPDATE " . DB_BANNED_IPS . "
      SET active=0
      WHERE id=$ipId
    ");

    if ($query)
    {
      cacheBannedFile();
      $data = array(
        "status" => 200,
        "status_html" => $inactiveHtml,
        "status_button_html" => banButton($ipId)
      );
      
      header("Content-type: application/json; charset=utf-8");
      echo json_encode($data);
      $conn->close();
      exit();
    }
  }
  elseif (isset($_POST['delete_ip']))
  {
    $query = $conn->query("DELETE FROM " . DB_BANNED_IPS . "
      WHERE id=$ipId
    ");

    if ($query)
    {
      cacheBannedFile();
      $data = array(
        "status" => 200
      );
      
      header("Content-type: application/json; charset=utf-8");
      echo json_encode($data);
      $conn->close();
      exit();
    }
  }
}

$totalIpsQuery = $conn->query("SELECT COUNT(id) AS countid FROM " . DB_BANNED_IPS);
$totalIpsData = $totalIpsQuery->fetch_array(MYSQLI_ASSOC);
$totalIps = $totalIpsData['countid'];
$limit = 50;

if (isset($_GET['ips_list']))
{
  $data = array(
    "status" => 200,
    "html" => ipsList(array(
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

function ipsList($Array=array())
{
  $escapeObj = new \SocialKit\Escape();
  $id = (isset($Array['id'])) ? (int) $Array['id'] : 0;
  $limit = (isset($Array['limit'])) ? (int) $Array['limit'] : 100;
  $start = (isset($Array['start'])) ? (int) $Array['start'] : 0;
  if ($start > 0) $start--;
  $start *= $limit;
  $search = (isset($Array['search'])) ? $escapeObj->stringEscape($Array['search']) : "";

  global $conn, $bannedHtml, $inactiveHtml;
  $ipsSql = "SELECT * FROM " . DB_BANNED_IPS . " WHERE id>0";

  if ($id > 0) $ipsSql .= " AND id=$id";

  if ($search !== "") $ipsSql .= " AND ip LIKE '%$search%'";

  $ipsSql .= " ORDER BY id DESC LIMIT $start,$limit";
  $ipsQuery = $conn->query($ipsSql);
  $html = '';
  $i = 0;
  while ($ip = $ipsQuery->fetch_array(MYSQLI_ASSOC))
  {
    $i++;
    $ipAdminObj = new \SocialKit\User();
    $ipAdminObj->setId($ip['admin_id']);
    $ipAdmin = $ipAdminObj->getRows();

    $status = ($ip['active'] == 0) ? $inactiveHtml : $bannedHtml;
    $button = ($ip['active'] == 0) ? banButton($ip['id']) : inactivateButton($ip['id']);

    $html .= '<tr id="ip_' . $ip['id'] . '">
      <td data-mobile="0">
        ' . $i . '
      </td>

      <td>
        <strong>' . $ip['ip'] . '</strong>
      </td>

      <td data-mobile="0">
        <a href="' . $ipAdmin['url'] . '" target="_blank">' . $ipAdmin['name'] . '</a>';

      if ($ipAdmin['verified'] == 1) $html .= ' <i class="verifiedBadge fa fa-check-circle"></i>';

      $html .= '</td>

      <td id="status_' . $ip['id'] . '" data-mobile="0">' . $status . '</td>

      <td>
        <span id="statusButton_' . $ip['id'] . '">' . $button . '</span> - <a onclick="deleteIp(' . $ip['id'] . ');" style="color:red;cursor:pointer;"><i class="fa fa-times-circle"></i> Delete</a>
      </td>
    </tr>';
  }

  return $html;
}

function banButton($ipId)
{
  return '<a onclick="activateIp(' . $ipId . ');" style="color:orange;cursor:pointer;"><i class="fa fa-ban"></i> Ban</a>';
}

function inactivateButton($ipId)
{
  return '<a onclick="inactivateIp(' . $ipId . ');" style="color:#30a21d;cursor:pointer;"><i class="fa fa-minus-circle"></i> Inactivate</a>';
}

function cacheBannedFile()
{
  global $conn;
  $content = '<?php
  $bannedIps = array();
  ';

  $bannedQuery = $conn->query("SELECT * FROM " . DB_BANNED_IPS . " WHERE active=1");
  while ($bannedFetch = $bannedQuery->fetch_array(MYSQLI_ASSOC))
  {
    $content .= '$bannedIps[] = \'' . $bannedFetch['ip'] . '\';
    ';
  }

  $content .= '?>';
  if (file_put_contents('../cache/banned_ips.php', $content)) return true;
  return false;
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

    <title>List IPs | Admin Panel</title>

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
                    <h2>Site Ips</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content shortNotice">
                    <input id="ban_ip_input" type="text" placeholder="Type an IP to add it to the list...">
                    <button onclick="banIp();">Ban IP</button>
                  </div>

                  <div class="x_content">
                    <table class="table table-hover ipTable">
                      <thead>
                        <tr>
                          <th data-mobile="0">#</th>
                          <th>IP</th>
                          <th data-mobile="0">Banned By</th>
                          <th data-mobile="0">Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="ipTableBody"></tbody>
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
    function banIp()
    {
      $.ajax({
        type: 'POST',
        url: '?',
        data: {
          ban_ip: 1,
          ip_addr: $("#ban_ip_input").val()
        },
        dataType: "json",
        beforeSend: function()
        {
          $("#ban_ip_input").val('');
        },
        success: function(result)
        {
          if (result.status == 200)
          {
            $("#ipTableBody").prepend(result.html);
            $(document.body).scrollTop(0);
          }
        }
      });
    }

    function deleteIp(ipid)
    {
      $.ajax({
        type: 'POST',
        url: '?',
        data: {
          delete_ip: 1,
          ip_id: ipid
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
            $("#ip_" + ipid).remove();
          }
        }
      });
    }

    function activateIp(ipid)
    {
      $.ajax({
        type: 'POST',
        url: '?',
        data: {
          activate_ip: 1,
          ip_id: ipid
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
            $("#status_" + ipid).html(result.status_html);
            $("#statusButton_" + ipid).html(result.status_button_html);
          }
        }
      });
    }

    function inactivateIp(ipid)
    {
      $.ajax({
        type: 'POST',
        url: '?',
        data: {
          inactivate_ip: 1,
          ip_id: ipid
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
            $("#status_" + ipid).html(result.status_html);
            $("#statusButton_" + ipid).html(result.status_button_html);
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
        totalPages: <?php echo ceil($totalIps/$limit); ?>,
        visiblePages: 15,
        onPageClick: function (event, page) {
          $.ajax({
            type: 'GET',
            url: '?',
            data: {
              ips_list: 1,
              limit: <?php echo $limit ?>,
              start: page,
              search: $("#ipSearchInput").val()
            },
            dataType: "json",
            beforeSend: function()
            {
              //
            },
            success: function(result)
            {
              if (result.status == 200) $("#ipTableBody").html(result.html);
              $(document.body).scrollTop(0);
            }
          });
        }
    };
    
    $(function () {
        $pagination.twbsPagination(defaultPagingOpts);

        $(document).on("keyup", "#ipSearchInput", function(){
          $("#ipTableBody").html('');
          searchQuery = $(this).val();
          clearTimeout(typingTimer);
          typingTimer = setTimeout(function(){
              userSearch(searchQuery);
          }, 300);
        });

        $(document).on("keydown", "#ipSearchInput", function(){
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
