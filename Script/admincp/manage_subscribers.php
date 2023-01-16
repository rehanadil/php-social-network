<?php require_once('admincore.php');
if (!isset($_GET['planid']))
{
  header("Location: subscription_plans.php");
  die();
}

$planid = (int) $_GET['planid'];
$pQuery = $conn->query("SELECT id FROM " . DB_SUBSCRIPTION_PLANS . " WHERE id=$planid");
if ($pQuery->num_rows != 1)
{
  header("Location: subscription_plans.php");
  die();
}

$totalUsersQuery = $conn->query("SELECT COUNT(id) AS countid FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT id FROM " . DB_USERS . " WHERE subscription_plan=$planid) AND type='user' AND active=1 AND banned=0");
$totalUsersData = $totalUsersQuery->fetch_array(MYSQLI_ASSOC);
$totalUsers = $totalUsersData['countid'];
$limit = 100;

if (isset($_GET['users_list']))
{
  $data = array(
    "status" => 200,
    "html" => usersList(array(
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

function usersList($Array=array())
  {
    global $planid;
    $escapeObj = new \SocialKit\Escape();
    $limit = (isset($Array['limit'])) ? (int) $Array['limit'] : 100;
    $start = ((isset($Array['start'])) ? (int) $Array['start'] : 0);
    if ($start > 0) $start--;
    $start *= $limit;
    $search = (isset($Array['search'])) ? $escapeObj->stringEscape($Array['search']) : "";

    global $conn;
    $usersSql = "SELECT id FROM " . DB_ACCOUNTS . " WHERE id IN (SELECT id FROM " . DB_USERS . " WHERE subscription_plan=$planid) AND type='user' AND active=1 AND banned=0";

    if ($search !== "") $usersSql .= " AND (name LIKE '%$search%' OR username='$search' OR email='$search')";

    $usersSql .= " ORDER BY time DESC LIMIT $start,$limit";
    $usersQuery = $conn->query($usersSql);
    $html = '';

    while ($users = $usersQuery->fetch_array(MYSQLI_ASSOC))
    {
      $userliObj = new \SocialKit\User();
      $userliObj->setId($users['id']);
      $userli = $userliObj->getRows();

      if ($userli['subscription_plan']['is_default'] == 1)
        $planIcon = "";
      else
        $planIcon = '<img src="' . $userli['subscription_plan']['plan_icon'] . '" width="16px">';

      $html .= '<tr>
        <td>
          <a href="' . $userli['avatar_url'] . '" target="_blank">
            <img src="' . $userli['thumbnail_url'] . '" width="24px">
          </a>
        </td>

        <td>
          <strong>
            <a href="' . $userli['url'] . '" target="_blank">' . $userli['name'] . '</a>
          </strong>';

        if ($userli['verified'] == 1) $html .= '<i class="verifiedBadge fa fa-check-circle"></i>';

        $html .= '</td>

        <td data-mobile="0">@' . $userli['username'] . '</td>

        <td data-mobile="0">' . $userli['email'] . '</td>

        <td data-mobile="0">
          ' . $planIcon . '
          <strong style="color:#' . $userli['subscription_plan']['plan_color'] . '">' . $userli['subscription_plan']['name'] . '</strong>
        </td>

        <td data-mobile="0">' . date('M j, Y h:i A', $userli['last_logged']) . '</td>

        <td data-mobile="0">' . date('M j, Y', $userli['time']) . '</td>

        <td>
          <a href="manage_user.php?id=' . $userli['id'] . '" style="color:#5890ff"><i class="fa fa-pencil-square"></i> Edit</a>
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

    <title>Manage Subscribers</title>

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
                    <h2>Manage Subscribers</h2>
                    <div class="clearfix"></div>
                  </div>

                  <div class="floatLeft">
                    <nav aria-label="Page navigation" align="left">
                        <ul class="pagination" id="paginationTop"></ul>
                    </nav>
                  </div>

                  <div class="floatRight" style="margin-top: 18px;">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input type="text" class="form-control has-feedback-left" id="userSearchInput" placeholder="Search...">
                        <span class="fa fa-search form-control-feedback left userSearchIcon" aria-hidden="true"></span>
                      </div>
                  </div>

                  <div class="floatClear"></div>

                  <div class="x_content">
                    <table class="table table-hover userTable">
                      <thead>
                        <tr>
                          <th>Avatar</th>
                          <th>Name</th>
                          <th data-mobile="0">Username</th>
                          <th data-mobile="0">Email</th>
                          <th data-mobile="0">Subscription Plan</th>
                          <th data-mobile="0">Last Online</th>
                          <th data-mobile="0">Joined On</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="userTableBody"></tbody>
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
    var typingTimer;
    var searchValue;
    var $pagination = $('#paginationTop, #paginationBottom');
    var defaultPagingOpts = {
        totalPages: <?php echo ceil($totalUsers/$limit); ?>,
        visiblePages: 15,
        onPageClick: function (event, page) {
          $.ajax({
            type: 'GET',
            url: '?',
            data: {
              planid: <?php echo $planid ?>,
              users_list: 1,
              limit: <?php echo $limit ?>,
              start: page,
              search: $("#userSearchInput").val()
            },
            dataType: "json",
            beforeSend: function()
            {
              //
            },
            success: function(result)
            {
              if (result.status == 200) $("#userTableBody").html(result.html);
              $(document.body).scrollTop(0);
            }
          });
        }
    };
    
    $(function () {
        $pagination.twbsPagination(defaultPagingOpts);

        $(document).on("keyup", "#userSearchInput", function(){
          $("#userTableBody").html('');
          searchQuery = $(this).val();
          clearTimeout(typingTimer);
          typingTimer = setTimeout(function(){
              userSearch(searchQuery);
          }, 300);
        });

        $(document).on("keydown", "#userSearchInput", function(){
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
