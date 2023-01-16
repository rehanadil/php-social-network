<?php
$storyObj->putReport();
$data = array(
    'status' => 200,
    'html' => $storyObj->getReportControlHtml()
);

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();