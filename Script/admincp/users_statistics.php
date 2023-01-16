<?php require_once('admincore.php');
$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
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

    <title>Users Statistics</title>

    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="vendors/nprogress/nprogress.css" rel="stylesheet">

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
          <div class="statTimezoneBtn">
            <a href="manage_user.php?id=<?php echo $user['id'] ?>">Change your timezone</a>
          </div>
          
          <?php
          $today = strtotime("today");
          $todayLabels = array();
          $todayTotalValues = array();
          $todayMaleValues = array();
          $todayFemaleValues = array();
          for ($i = 0; $i < 24; $i++)
          {
            $hour = $today + (60 * 60 * $i);
            $nextHour = $hour + (60 * 60);
            $todayLabels[] = date('h A', $hour);

            $todayTotalValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time BETWEEN $hour AND $nextHour");
            $todayTotalValuesFetch = $todayTotalValuesQuery->fetch_array(MYSQLI_ASSOC);
            $todayTotalValues[] = $todayTotalValuesFetch['count'];

            $todayMaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time BETWEEN $hour AND $nextHour");
            $todayMaleValuesFetch = $todayMaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $todayMaleValues[] = $todayMaleValuesFetch['count'];

            $todayFemaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time BETWEEN $hour AND $nextHour");
            $todayFemaleValuesFetch = $todayFemaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $todayFemaleValues[] = $todayFemaleValuesFetch['count'];
          }

          $todayTotalQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time>$today");
          $todayTotalFetch = $todayTotalQuery->fetch_array(MYSQLI_ASSOC);

          $todayMaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time>$today");
          $todayMaleFetch = $todayMaleQuery->fetch_array(MYSQLI_ASSOC);

          $todayFemaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time>$today");
          $todayFemaleFetch = $todayFemaleQuery->fetch_array(MYSQLI_ASSOC);
          ?>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>Today <small>(Total: <?php echo $todayTotalFetch['count']; ?>; Male: <?php echo $todayMaleFetch['count']; ?>; Female: <?php echo $todayFemaleFetch['count']; ?>)</small></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <canvas class="statChart" id="todayChart" width="100" height="20"></canvas>
                </div>
              </div>
            </div>
          </div>

          <?php
          $week = strtotime('-' . date('w') . ' days');
          $weekLabels = array();
          $weekTotalValues = array();
          $weekMaleValues = array();
          $weekFemaleValues = array();
          for ($i = 0; $i < 7; $i++)
          {
            $day = $week + (60 * 60 * 24 * $i);
            $nextDay = $day + (60 * 60 * 24 * 1);
            $weekLabels[] = date('l', $day);

            $weekTotalValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time BETWEEN $day AND $nextDay");
            $weekTotalValuesFetch = $weekTotalValuesQuery->fetch_array(MYSQLI_ASSOC);
            $weekTotalValues[] = $weekTotalValuesFetch['count'];

            $weekMaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time BETWEEN $day AND $nextDay");
            $weekMaleValuesFetch = $weekMaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $weekMaleValues[] = $weekMaleValuesFetch['count'];

            $weekFemaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time BETWEEN $day AND $nextDay");
            $weekFemaleValuesFetch = $weekFemaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $weekFemaleValues[] = $weekFemaleValuesFetch['count'];
          }
          $weekQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time>$week");
          $weekFetch = $weekQuery->fetch_array(MYSQLI_ASSOC);

          $weekMaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time>$week");
          $weekMaleFetch = $weekMaleQuery->fetch_array(MYSQLI_ASSOC);

          $weekFemaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time>$week");
          $weekFemaleFetch = $weekFemaleQuery->fetch_array(MYSQLI_ASSOC);
          ?>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>This Week <small>(Total: <?php echo $weekFetch['count']; ?>; Male: <?php echo $weekMaleFetch['count']; ?>; Female: <?php echo $weekFemaleFetch['count']; ?>)</small></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <canvas class="statChart" id="weekChart" width="100" height="20"></canvas>
                </div>
              </div>
            </div>
          </div>

          <?php
          $daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'));
          $month = strtotime("1 " . date('F Y'));
          $monthLabels = array();
          $monthTotalValues = array();
          $monthMaleValues = array();
          $monthFemaleValues = array();
          for ($i = 0; $i < $daysInMonth; $i++)
          {
            $day = $month + (60 * 60 * 24 * $i);
            $nextDay = $day + (60 * 60 * 24 * 1);
            $monthLabels[] = date('d', $day);

            $monthTotalValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time BETWEEN $day AND $nextDay");
            $monthTotalValuesFetch = $monthTotalValuesQuery->fetch_array(MYSQLI_ASSOC);
            $monthTotalValues[] = $monthTotalValuesFetch['count'];

            $monthMaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time BETWEEN $day AND $nextDay");
            $monthMaleValuesFetch = $monthMaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $monthMaleValues[] = $monthMaleValuesFetch['count'];

            $monthFemaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time BETWEEN $day AND $nextDay");
            $monthFemaleValuesFetch = $monthFemaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $monthFemaleValues[] = $monthFemaleValuesFetch['count'];
          }
          $monthQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time>$month");
          $monthFetch = $monthQuery->fetch_array(MYSQLI_ASSOC);

          $monthMaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time>$month");
          $monthMaleFetch = $monthMaleQuery->fetch_array(MYSQLI_ASSOC);

          $monthFemaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time>$month");
          $monthFemaleFetch = $monthFemaleQuery->fetch_array(MYSQLI_ASSOC);
          ?>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>This Month <small>(Total: <?php echo $monthFetch['count']; ?>; Male: <?php echo $monthMaleFetch['count']; ?>; Female: <?php echo $monthFemaleFetch['count']; ?>)</small></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <canvas class="statChart" id="monthChart" width="100" height="20"></canvas>
                </div>
              </div>
            </div>
          </div>

          <?php
          $iYear = 0;
          $year = strtotime("1 January " . date('Y'));
          $yearLabels = array();
          $yearTotalValues = array();
          $yearMaleValues = array();
          $yearFemaleValues = array();
          foreach ($months as $yearMonth)
          {
            $daysInThisMonth = cal_days_in_month(CAL_GREGORIAN, ($iYear+1), date('Y'));
            $month = $year + (60 * 60 * 24 * $daysInThisMonth * $iYear);
            $nextMonth = $month + (60 * 60 * 24 * $daysInThisMonth * 1);
            $yearLabels[] = $yearMonth;

            $yearTotalValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time BETWEEN $month AND $nextMonth");
            $yearTotalValuesFetch = $yearTotalValuesQuery->fetch_array(MYSQLI_ASSOC);
            $yearTotalValues[] = $yearTotalValuesFetch['count'];

            $yearMaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time BETWEEN $month AND $nextMonth");
            $yearMaleValuesFetch = $yearMaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $yearMaleValues[] = $yearMaleValuesFetch['count'];

            $yearFemaleValuesQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time BETWEEN $month AND $nextMonth");
            $yearFemaleValuesFetch = $yearFemaleValuesQuery->fetch_array(MYSQLI_ASSOC);
            $yearFemaleValues[] = $yearFemaleValuesFetch['count'];
            $iYear++;
          }
          $yearQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND active=1 AND time>$year");
          $yearFetch = $yearQuery->fetch_array(MYSQLI_ASSOC);

          $yearMaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='male') AND active=1 AND time>$year");
          $yearMaleFetch = $yearMaleQuery->fetch_array(MYSQLI_ASSOC);

          $yearFemaleQuery = $conn->query("SELECT COUNT(id) AS count FROM " . DB_ACCOUNTS . " WHERE type='user' AND id IN (SELECT id FROM " . DB_USERS . " WHERE gender='female') AND active=1 AND time>$year");
          $yearFemaleFetch = $yearFemaleQuery->fetch_array(MYSQLI_ASSOC);
          ?>
          <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel">
                <div class="x_title">
                  <h2>This Year <small>(Total: <?php echo $yearFetch['count']; ?>; Male: <?php echo $yearMaleFetch['count']; ?>; Female: <?php echo $yearFemaleFetch['count']; ?>)</small></h2>
                  <div class="clearfix"></div>
                </div>
                <div class="x_content">
                  <canvas class="statChart" id="yearChart" width="100" height="20"></canvas>
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
    <!-- Chart.js -->
    <script src="vendors/Chart.js/dist/Chart.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.js"></script>

    <script>
      Chart.defaults.global.legend = {
        enabled: false
      };

      new Chart($("#todayChart"), {
        type: 'bar',
        data: {
          labels: [<?php echo '"' . implode('","', $todayLabels) . '"' ?>],
          datasets: [
            {
              type: 'bar',
              label: 'Male Users',
              backgroundColor: "#36a2eb",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $todayMaleValues) ?>]
            },
            {
              type: 'bar',
              label: 'Female Users',
              backgroundColor: "#ff66cc",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $todayFemaleValues) ?>]
            },
            {
              type: 'line',
              label: 'Total Users',
              backgroundColor: "#c3bfe6",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $todayTotalValues) ?>]
            }
          ]
        },

        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                userCallback: function(label, index, labels) {
                  // when the floored value is the same as the value we have a whole number
                  if (Math.floor(label) === label) {
                    return label;
                  }
                }
              }
            }]
          }
        }
      });

      new Chart($("#weekChart"), {
        type: 'bar',
        data: {
          labels: [<?php echo '"' . implode('","', $weekLabels) . '"' ?>],
          datasets: [
            {
              type: 'bar',
              label: 'Male Users',
              backgroundColor: "#36a2eb",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $weekMaleValues) ?>]
            },
            {
              type: 'bar',
              label: 'Female Users',
              backgroundColor: "#ff66cc",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $weekFemaleValues) ?>]
            },
            {
              type: 'line',
              label: 'Total Users',
              backgroundColor: "#c3bfe6",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $weekTotalValues) ?>]
            }
          ]
        },

        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                userCallback: function(label, index, labels) {
                  // when the floored value is the same as the value we have a whole number
                  if (Math.floor(label) === label) {
                    return label;
                  }
                }
              }
            }]
          }
        }
      });

      new Chart($("#monthChart"), {
        type: 'bar',
        data: {
          labels: [<?php echo '"' . implode('","', $monthLabels) . '"' ?>],
          datasets: [
            {
              type: 'bar',
              label: 'Male Users',
              backgroundColor: "#36a2eb",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $monthMaleValues) ?>]
            },
            {
              type: 'bar',
              label: 'Female Users',
              backgroundColor: "#ff66cc",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $monthFemaleValues) ?>]
            },
            {
              type: 'line',
              label: 'Total Users',
              backgroundColor: "#c3bfe6",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $monthTotalValues) ?>]
            }
          ]
        },

        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                userCallback: function(label, index, labels) {
                  // when the floored value is the same as the value we have a whole number
                  if (Math.floor(label) === label) {
                    return label;
                  }
                }
              }
            }]
          }
        }
      });

      new Chart($("#yearChart"), {
        type: 'bar',
        data: {
          labels: [<?php echo '"' . implode('","', $yearLabels) . '"' ?>],
          datasets: [
            {
              type: 'bar',
              label: 'Male Users',
              backgroundColor: "#36a2eb",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $yearMaleValues) ?>]
            },
            {
              type: 'bar',
              label: 'Female Users',
              backgroundColor: "#ff66cc",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $yearFemaleValues) ?>]
            },
            {
              type: 'line',
              label: 'Total Users',
              backgroundColor: "#c3bfe6",
              borderColor: "#ffffff",
              borderWidth: 2,
              data: [<?php echo implode(',', $yearTotalValues) ?>]
            }
          ]
        },

        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                userCallback: function(label, index, labels) {
                  // when the floored value is the same as the value we have a whole number
                  if (Math.floor(label) === label) {
                    return label;
                  }
                }
              }
            }]
          }
        }
      });
    </script>
  </body>
</html>