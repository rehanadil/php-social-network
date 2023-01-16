<?php
$data = array("status" => 417);

$abstractLanguageQuery = $conn->query("SELECT DISTINCT type FROM " . DB_LANGUAGES . " LIMIT 1");
if ($abstractLanguageQuery->num_rows != 1) header("Location: index.php");
$abstractLanguageData = $abstractLanguageQuery->fetch_array(MYSQLI_ASSOC);
$abstractLanguage = $abstractLanguageData['type'];

$limit = (isset($_GET['limit'])) ? (int) $_GET['limit'] : 200;
$start = (isset($_GET['start'])) ? (int) $_GET['start'] : 1;
$start = ($start - 1) * $limit;
$languageQuery = $conn->query("SELECT * FROM " . DB_LANGUAGES . " WHERE type='$abstractLanguage' ORDER BY keyword ASC LIMIT $start,$limit");
$html = "";

while ($row = $languageQuery->fetch_array(MYSQLI_ASSOC))
{
  $displayName = ucwords(str_replace(array('pcat_', '_'), array('', ' '), $row['keyword']));
  $html .= '<div class="form-group">
    <label for="text" class="control-label col-md-3 col-sm-3 col-xs-12">' . $displayName . ' <span class="required">*</span>
      <br><small>(' . $row['keyword'] . ')</small>
    </label>

    <div class="col-md-6 col-sm-6 col-xs-12 ' . $row['keyword'] . '">
      <textarea id="' . $row['keyword'] . '" name="keywords[' . $row['keyword'] . ']" required="required" placeholder="Type your translation here" class="form-control col-md-7 col-xs-12" style="resize:vertical" onkeyup="editLanguageKey(\'' . $row['keyword'] . '\')"></textarea>

      <i id="' . $row['keyword'] . 'Icon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>

      <div class="rebuildNotice">After editing all of your translations, make sure you <strong>Rebuild</strong> to apply the changes to your website UI.</div>
    </div>
  </div>';
}

if (!empty($html))
{
  $data = array(
    "status" => 200,
    "html" => $html
  );
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();
?>