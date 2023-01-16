<?php
function reportsList($Array=array())
{
  $escapeObj = new \SocialKit\Escape();
  $limit = (isset($Array['limit'])) ? (int) $Array['limit'] : 100;
  $start = ((isset($Array['start'])) ? (int) $Array['start'] : 0);
  if ($start > 0) $start--;
  $start *= $limit;
  $search = (isset($Array['search'])) ? $escapeObj->stringEscape($Array['search']) : "";

  global $conn, $lang, $config;
  $reportsSql = "SELECT * FROM " . DB_REPORTS . " WHERE active=1";

  if ($search !== "") $reportsSql .= " AND reporter_id IN (SELECT id FROM " . DB_ACCOUNTS . " WHERE name LIKE '%$search%' AND banned=0)";

  $reportsSql .= " ORDER BY id DESC LIMIT $start,$limit";
  $reportsQuery = $conn->query($reportsSql);
  $html = '';
  while ($report = $reportsQuery->fetch_array(MYSQLI_ASSOC))
  {
    $reporterliObj = new \SocialKit\User();
    $reporterliObj->setId($report['reporter_id']);
    $reporter = $reporterliObj->getRows();

    $html .= '<tr id="report_' . $report['id'] . '">
      <td>
        <a href="' . $reporter['avatar_url'] . '" target="_blank">
          <img src="' . $reporter['thumbnail_url'] . '" width="24px">
        </a>
      </td>

      <td>
        <strong>
          <a href="' . $reporter['url'] . '" target="_blank">' . $reporter['name'] . '</a>
        </strong>';

      if ($reporter['verified'] == 1) $html .= '<i class="verifiedBadge fa fa-check-circle"></i>';

      $html .= '</td>

      <td data-mobile="0">' . date('M j, Y', strtotime($report['timestamp'])) . '</td>

      <td>
        <a href="' . $config['site_url'] . '/?a=story&id=' . $report['post_id'] . '#' . $report['type'] .'_' . $report['id'] . '" target="_blank">
          Show Post
        </a>
      </td>';

      $html .= '<td id="action_' . $report['id'] . '">';
        if ($report['status'] == 0)
          $html .= '<a onclick="updateReportStatus(' . $report['id'] . ', 1);" style="color:#5890ff;cursor:pointer"><i class="fa fa-check"></i> Mark Safe</a> - <a onclick="updateReportStatus(' . $report['id'] . ', 2);" style="color:red;cursor:pointer"><i class="fa fa-trash"></i> Delete Post</a>';
        else
          $html .= 'None';
      $html .= '</td>';

      $html .= '<td data-mobile="0" id="status_' . $report['id'] . '">';
        if ($report['status'] == 1)
          $html .= 'Marked Safe';
        elseif ($report['status'] == 2)
          $html .= 'Deleted';
        else
          $html .= 'Pending';
      $html .= '</td>
    </tr>';
  }
  return $html;
}

$data = array(
  "status" => 200,
  "html" => reportsList(array(
    'limit' => (isset($_GET['limit'])) ? (int) $_GET['limit'] : 100,
    'start' => (isset($_GET['start'])) ? (int) $_GET['start'] : 0,
    'search' => (isset($_GET['search'])) ? $_GET['search'] : ""
  ))
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();
?>