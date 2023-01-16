<?php
set_time_limit(0);
@ini_set("memory_limit", "-1");
require_once('../assets/includes/definitions.php');
require_once('autoload.php');

// Include all core functions
$functions = glob('../assets/includes/functions/*.php');
foreach ($functions as $func) require_once($func);

$installErrors = array();
$pc = "";
$mysqlHost = "";
$mysqlUser = "";
$mysqlPassword = "";
$mysqlDatabase = "";
$siteUrl = "";
$siteName = "";
$siteTitle = "";
$siteEmail = "";
$adminUsername = "";
$adminPassword = "";
$install = false;
if (!isset($_GET['site'])) $_GET['site'] = "";

if (isset($_POST['install']))
{
  if (!empty($_POST['pc'])
    && !empty($_POST['mysql_host'])
    && !empty($_POST['mysql_username'])
    && isset($_POST['mysql_password'])
    && !empty($_POST['mysql_dbname'])
    && !empty($_POST['site_url'])
    && !empty($_POST['site_name'])
    && !empty($_POST['site_title'])
    && !empty($_POST['site_email'])
    && !empty($_POST['admin_username'])
    && !empty($_POST['admin_password']))
  {
    $pc = trim($_POST['pc']);
    $mysqlHost = trim($_POST['mysql_host']);
    $mysqlUser = trim($_POST['mysql_username']);
    $mysqlPassword = trim($_POST['mysql_password']);
    $mysqlDatabase = trim($_POST['mysql_dbname']);
    $siteUrl = trim($_POST['site_url']);
    $siteName = trim($_POST['site_name']);
    $siteTitle = trim($_POST['site_title']);
    $siteEmail = trim($_POST['site_email']);
    $adminUsername = trim($_POST['admin_username']);
    $adminPassword = trim($_POST['admin_password']);
    $install = true;

    if (!filter_var($siteUrl, FILTER_VALIDATE_URL))
    {
      $installErrors[] = "Invalid Website URL. Use http or https (if you have SSL)";
      $install = false;
    }

    if ($install)
    {
      // Connect to SQL Server
      $conn = new mysqli($mysqlHost, $mysqlUser, $mysqlPassword, $mysqlDatabase);
      $conn->set_charset("utf8");
      if ($conn->connect_errno)
      {
        $installErrors[] = "MySQL Connect Error: " . $conn->error;
      }
      else
      {
        $siteUrl = preg_replace('/(.*?)\/$/i', '$1', $siteUrl);
        $adminUsername = preg_replace('/[^A-Za-z0-9_]/i', '', $adminUsername);
        $pstdt = array('pc' => $pc, 'site' => $siteUrl);
        $scco = array(
          'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($pstdt)
          )
        );
        $vrf = file_get_contents(base64_decode("aHR0cDovL3ZlcmlmeS5zb2NpYWxraXQubmV0Lw=="), false, stream_context_create($scco));
        if (!$vrf)
        {
          $installErrors[] = "Failed to connect to server. Please try again later or <a href='http://support.socialkit.net'>Contact Us</a>";
        }
        else
        {
          $vrfd = json_decode($vrf, true);
          if ($vrfd['status'] === "SUCCESS")
          {
            $configContent = '<?php
            // MySQL Hostname / Server (for eg: "localhost")
            $sql_host = "' . $mysqlHost . '";


            // MySQL Database Name
            $sql_name = "' . $mysqlDatabase . '";


            // MySQL Database User
            $sql_user = "' . $mysqlUser . '";


            // MySQL Database Password
            $sql_pass = "' . $mysqlPassword . '";


            // Site URL
            $site_url = "' . $siteUrl . '"; // You must write \"http://\", or \"https://\" if you have SSL

            // Purchase Code
            $pc = "' . $pc . '"; // Your purchase code, do NOT share it with anyone but the author.
            ';
            $writeConfig = file_put_contents('../assets/includes/config.php', $configContent);
            if ($writeConfig)
            {
              $sqlFile = 'socialkit.sql';
              $templine = '';
              $lines = file($sqlFile);
              $query = false;
              
              foreach ($lines as $line)
              {
                 if (substr($line, 0, 2) == '--' || $line == '') continue;
                    
                 $templine .= $line;
                 $query = false;
                 
                 if (substr(trim($line), -1, 1) == ';')
                 {
                    $query = $conn->query($templine);
                    $templine = ''; 
                 }
              }

              if ($query)
              {
                $conn->query("UPDATE configurations
                  SET site_name='$siteName',
                  site_title='$siteTitle',
                  email='$siteEmail'
                ");

                $registerAdmin = new \SocialKit\registerUser();
                $registerAdmin->setUsername($adminUsername);
                $registerAdmin->setName('Admin');
                $registerAdmin->setPassword($adminPassword);
                $registerAdmin->setEmail($siteEmail);
                $registerAdmin->setGender('male');
                
                if ($register = $registerAdmin->register())
                {
                  $adminId = $register['id'];
                  $conn->query("UPDATE " . DB_USERS . " SET admin=1,start_up=1 WHERE id=" . $adminId);
                  $conn->query("UPDATE " . DB_ACCOUNTS . " SET email_verified=1 WHERE id=" . $adminId);
                }

                header("Location: ?step=finish&site=" . base64_encode($siteUrl));
              }
              else
              {
                $installErrors[] = "Failed to write SQL query. Please make sure your database is empty.";
              }
            }
            else
            {
              $installErrors[] = "Failed to write to config.php";
            }
          }
          else
          {
            $installErrors[] = $vrfd['error_message'];
          }
        }
      }
    }
  }
  else
  {
    $installErrors[] = "Please fill in all the details";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>SocialKit Installation</title>
  <meta name="robots" content="noindex">
  <meta name="googlebot" content="noindex">
  <link rel="shortcut icon" href="../themes/socialkit/images/logo/mini.png">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="../themes/socialkit/css/font-awesome/css/font-awesome.min.css">
  <script src="../themes/socialkit/javascript/jquery.1.10.2.min.js"></script>
</head>

<body>
  <div class="installerContainer">
    <div class="floatLeft">
      <?php
      $step = 'terms';
      $steps_array = array(
        'requirements',
        'terms',
        'installation',
        'finish'
      );
      if (!empty($_GET['step']))
      {
        if (in_array($_GET['step'], $steps_array)) $step = $_GET['step'];
      }

      $cURL = true;
      $php = true;
      $gd = true;
      $disabled = false;
      $mysqli = true;
      $is_writable = true;
      $mbstring = true;
      $is_htaccess = true;
      $is_mod_rewrite = true;
      $is_sql = true;
      $zip = true;
      $allow_url_fopen = true;
      $exif_read_data = true;
      $is_statistics = true;

      if (!version_compare(PHP_VERSION, '5.4.0', '>='))
      {
        $php = false;
        $disabled = true;
      }
      if (!function_exists('mysqli_connect'))
      {
        $mysqli = false;
        $disabled = true;
      }
      if (!function_exists('curl_init'))
      {
        $cURL = false;
        $disabled = true;
      }
      if (!extension_loaded('gd')
        && !function_exists('gd_info'))
      {
        $gd = false;
        $disabled = true;
      }
      if (!extension_loaded('mbstring'))
      {
        $mbstring = false;
        $disabled = true;
      }
      if (!extension_loaded('zip'))
      {
        $zip = false;
        $disabled = true;
      }
      if(!ini_get('allow_url_fopen'))
      {
        $allow_url_fopen = false;
        $disabled = true;
      }
      if (!file_exists('../.htaccess'))
      {
        $is_htaccess = false;
        $disabled = true;
      }
      if (!file_exists('socialkit.sql'))
      {
        $is_sql = false;
        $disabled = true;
      }
      if (!is_writable('../assets/includes/config.php'))
      {
        $is_writable = false;
        $disabled = true;
      }
      if (!function_exists('cal_days_in_month'))
      {
        $is_statistics = false;
        $disabled = true;
      }
      if (!function_exists('exif_read_data'))
      {
        $is_exifread = false;
        $disabled = true;
      }
      ?>
      <div class="installMenu">
        <div class="menuLi<?php if ($step === "terms") echo " current" ?>">
          <i class="fa fa-fw fa-bars"></i> Terms Of Use
        </div>

        <div class="menuLi<?php if ($step === "requirements") echo " current" ?>">
          <i class="fa fa-fw fa-gears"></i> Requirements
        </div>

        <div class="menuLi<?php if ($step === "installation") echo " current" ?>">
          <i class="fa fa-fw fa-cloud-upload"></i> Installation
        </div>

        <div class="menuLi<?php if ($step === "finish") echo " current" ?>">
          <i class="fa fa-fw fa-check"></i> Finish
        </div>
      </div>
    </div>

    <div class="floatRight">
      
      <?php if ($step === "terms") { ?>

      <div class="installContentContainer">
        <div class="header"><i class="fa fa-fw fa-bars"></i> Terms Of Use</div>

        <div class="installContent">

          <div class="line font16"><strong>LICENSE USAGE: One (1) License per (1) Domain</strong></div>

          <div class="para">
            <div class="font15"><strong>You CAN:</strong></div>
            <div class="font14">1) Use on one (1) domain only, additional license purchase required for each additional domain.</div>
            <div class="font14">2) Modify or edit as you see fit.</div>
            <div class="font14">3) Delete sections as you see fit.</div>
            <div class="font14">4) Translate to your choice of language.</div>
          </div>

          <div class="para">
            <div class="font15"><strong>You CANNOT:</strong></div>
            <div class="font14">1) Resell, distribute, give away or trade by any means to any third party or individual.</div>
            <div class="font14">2) Use on more than one (1) domain.</div>
          </div>

          <div class="para">
            <div class="font15">You may purchase as many licenses as you want.</div>
          </div>
          <hr>

          <div class="line">
            <input id="agreeTerm" type="checkbox"> I agree to the Terms Of Use
          </div>

          <div class="line">
            <a id="nextBtn" class="btn" disabled><i class="fa fa-arrow-right"></i> Next</a>
          </div>

        </div>
      </div>

      <script>
      $(document).on("click", "a", function(event){
        aDisabled = $(this).attr("disabled");
        if (typeof aDisabled !== "undefined") event.preventDefault();
      });
      $(document).on("change", "#agreeTerm", function(){
        if ($("#agreeTerm:checked").length == 1)
          $("#nextBtn").removeAttr("disabled href").attr("href", "?step=requirements");
        else
          $("#nextBtn").removeAttr("href").attr("disabled", true);
      });
      </script>

      <?php } elseif ($step === "requirements") { ?>

      <div class="installContentContainer">
        <div class="header"><i class="fa fa-fw fa-gears"></i> Requirements</div>

        <div class="installContent">

          <table class="requirementTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody class="font13">
            <tr>
              <td>PHP 5.4+</td>
              <td>Required PHP version 5.4 or more</td>
              <td><?php echo ($php == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>MySQLi</td>
              <td>Required MySQLi PHP extension</td>
              <td><?php echo ($mysqli == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>cURL</td>
              <td>Required cURL PHP extension</td>
              <td><?php echo ($cURL == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>GD Library</td>
              <td>Required GD Library for image cropping</td>
              <td><?php echo ($gd == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>Mbstring</td>
              <td>Required Mbstring extension for UTF-8 Strings</td>
              <td><?php echo ($mbstring == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>ZIP</td>
              <td>Required ZIP extension for backuping data</td>
              <td><?php echo ($zip == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>cal_days_in_month</td>
              <td>Required cal_days_in_month extension for statistics data</td>
              <td><?php echo ($is_statistics == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Installed</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not installed</font>'?></td>
            </tr>

            <tr>
              <td>allow_url_fopen</td>
              <td>Required allow_url_fopen</td>
              <td><?php echo ($allow_url_fopen == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Enabled</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Disabled</font>'?></td>
            </tr>

            <tr>
              <td>.htaccess</td>
              <td>Required .htaccess file for script security <small>(Located in ./Script)</small></td>
              <td><?php echo ($is_htaccess == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Uploaded</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not uploaded</font>'?></td>
            </tr>

            <tr>
              <td>socialkit.sql</td>
              <td>Required socialkit.sql for the installation <small>(Located in ./Script/install)</small></td>
              <td><?php echo ($is_sql == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Uploaded</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not uploaded</font>'?></td>
            </tr>

            <tr>
              <td>config.php</td>
              <td>Required config.php to be writable for the installation</td>
              <td><?php echo ($is_writable == true) ? '<font color="green"><i class="fa fa-check fa-fw"></i> Writable</font>' : '<font color="red"><i class="fa fa-times fa-fw"></i> Not writable</font>'?></td>
            </tr>
          </tbody>
          </table>

          <div class="line">
            <a id="nextBtn" class="btn" <?php echo ($disabled) ? 'disabled' : 'href="?step=installation"' ?>><i class="fa fa-arrow-right"></i> Next</a>
          </div>

        </div>
      </div>

      <?php } elseif ($step === "installation") { ?>

      <div class="installContentContainer">
        <div class="header"><i class="fa fa-fw fa-cloud-upload"></i> Installation</div>

        <div class="installContent">

          <div class="errorThrows">
            <ul>
              <?php
              foreach ($installErrors as $error) { ?>
              <li><?php echo $error ?></li>
              <?php } ?>
            </ul>
          </div>
          
          <form class="font13" method="post" action="?step=installation">

            <div class="formInputContent">
              <label class="floatLeft">
                  Purchase Code:
              </label>

              <div class="input floatLeft">
                  <input type="text" name="pc" value="<?php echo $pc ?>" autocomplete="off">
              </div>

              <div class="floatClear"></div>
            </div>

            <hr>

            <div class="formInputContent">
              <label class="floatLeft">
                  MySQL Hostname
              </label>

              <div class="input floatLeft">
                  <input type="text" name="mysql_host" value="<?php echo $mysqlHost ?>" autocomplete="off">
                  <small>localhost, for example</small>
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  MySQL Username
              </label>

              <div class="input floatLeft">
                  <input type="text" name="mysql_username" value="<?php echo $mysqlUser ?>" autocomplete="off">
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  MySQL Password
              </label>

              <div class="input floatLeft">
                  <input type="text" name="mysql_password" value="<?php echo $mysqlPassword ?>" autocomplete="off">
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  MySQL Database Name
              </label>

              <div class="input floatLeft">
                  <input type="text" name="mysql_dbname" value="<?php echo $mysqlDatabase ?>" autocomplete="off">
              </div>

              <div class="floatClear"></div>
            </div>

            <hr>

            <div class="formInputContent">
              <label class="floatLeft">
                  Website URL
              </label>

              <div class="input floatLeft">
                  <input type="text" name="site_url" value="<?php echo $siteUrl ?>" autocomplete="off">
                  <small>for eg. http://www.yoursite.com</small>
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  Website Name
              </label>

              <div class="input floatLeft">
                  <input type="text" name="site_name" value="<?php echo $siteName ?>" autocomplete="off">
                  <small>SocialKit, for example</small>
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  Website Title
              </label>

              <div class="input floatLeft">
                  <input type="text" name="site_title" value="<?php echo $siteTitle ?>" autocomplete="off">
                  <small>SocialKit - The Ultimate Social Network, for example</small>
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  Website Email
              </label>

              <div class="input floatLeft">
                  <input type="text" name="site_email" value="<?php echo $siteEmail ?>" autocomplete="off">
                  <small>contact@yoursite.com, for example</small>
              </div>

              <div class="floatClear"></div>
            </div>

            <hr>

            <div class="formInputContent">
              <label class="floatLeft">
                  Admin Username
              </label>

              <div class="input floatLeft">
                  <input type="text" name="admin_username" value="<?php echo $adminUsername ?>" autocomplete="off">
                  <small>rehanadil, for example</small>
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft">
                  Admin Password
              </label>

              <div class="input floatLeft">
                  <input type="text" name="admin_password" value="<?php echo $adminPassword ?>" autocomplete="off">
              </div>

              <div class="floatClear"></div>
            </div>

            <div class="formInputContent">
              <label class="floatLeft"></label>

              <div class="input floatLeft">
                  <input type="hidden" name="install" value="1">
                  <button class="btn"><i class="fa fa-cloud-upload"></i> Install</button>
                  <span class="note"><i class="fa fa-info-circle"></i> <strong>Note:</strong> This will take a while</span>
              </div>

              <div class="floatClear"></div>
            </div>

          </form>

        </div>
      </div>

      <?php } elseif ($step === "finish") { ?>
      <div class="installContentContainer">
        <div class="header"><i class="fa fa-fw fa-check"></i> Finished</div>

        <div class="installContent">
          <div class="line font16">
            <strong>Congratulations!</strong> SocialKit has been successfully installed in your server.
          </div>

          <div class="para" align="center">
            <a class="btn success" href="<?php echo base64_decode($_GET['site']) ?>">
              <i class="fa fa-fw fa-sign-in"></i>
              Start Using!
            </a>
          </div>

          <div class="para"></div>
        </div>
      </div>
      <?php } ?>
    </div>

    <div class="floatClear"></div>
  </div>
</body>
</html>