<?php
$data = array("status" => 417);

if (isset($_GET['id']))
{
  $id = $escapeObj->stringEscape($_GET['id']);
  $limit = (isset($_GET['limit'])) ? (int) $_GET['limit'] : 100;
  $start = (isset($_GET['start'])) ? (int) $_GET['start'] : 1;
  $start = ($start - 1) * $limit;
  $languageQuery = $conn->query("SELECT * FROM " . DB_LANGUAGES . " WHERE type='$id' ORDER BY keyword ASC LIMIT $start,$limit");
  $html = "";
  $disabled = "";
  if (!empty($_GET['delete'])) $disabled = " disabled";

  while ($row = $languageQuery->fetch_array(MYSQLI_ASSOC))
  {
    $rowKeyword = $row['keyword'];
    $displayName = ucwords(str_replace(array('pcat_', '_'), array('', ' '), $rowKeyword));
    $html .= '<div id="' . $rowKeyword . '" class="form-group">
      <label class="control-label col-md-3 col-sm-3 col-xs-12">' . $displayName . ' <span class="required">*</span>
        <br><small>(' . $rowKeyword . ')</small>
      </label>

      <div class="col-md-6 col-sm-6 col-xs-12 ' . $rowKeyword . '">
        <textarea name="' . $rowKeyword . '" required="required" placeholder="Type your translation here" class="form-control col-md-7 col-xs-12" style="resize:vertical" onkeyup="editLanguageKey(\'' . $rowKeyword . '\')"' . $disabled . '>' . $row['text'] . '</textarea>

        <i id="' . $rowKeyword . 'Icon" class="fa fa-check-circle hideIcon" data-icon="check-circle"></i>

        <div class="rebuildNotice">After editing all of your translations, make sure you <strong>Rebuild</strong> to apply the changes to your website UI.</div>
      </div>
    </div>';
  }
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