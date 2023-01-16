<div class="top_nav">
  <div class="nav_menu">
    <nav>
      <div class="nav toggle">
        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
      </div>

      <ul class="nav navbar-nav navbar-right">
        <li>
          <a href="<?php echo $user['url'] ?>" class="user-profile dropdown-toggle">
            <img src="<?php echo $user['thumbnail_url'] ?>" alt="<?php echo $user['name'] ?>"><?php echo $user['name'] ?>
          </a>
        </li>

        <li role="presentation" class="dropdown">
          <a href="manage_reports.php" class="dropdown-toggle info-number">
            <i class="fa fa-info-circle"></i>
            <?php
            $newReportsQuery = $conn->query("SELECT COUNT(id) AS cid FROM " . DB_REPORTS . " WHERE status=0 AND active=1");
            $newReports = $newReportsQuery->fetch_array(MYSQLI_ASSOC);
            ?>
            <span class="badge bg-red"><?php echo $newReports['cid'] ?></span>
          </a>
        </li>

        <li role="presentation" class="dropdown">
          <a href="<?php echo smoothLink('index.php?a=home') ?>" class="dropdown-toggle info-number">
            <i class="fa fa-home"></i>
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <?php if (file_exists('../install')) { ?>
  <div class="deleteInstallNotice">
    <i class="fa fa-info-circle"></i> For security reasons, please delete "install" folder from your server.
  </div>
  <?php } ?>
</div>
<!-- /top navigation -->