<?php
/**
 * @package Social Kit - Social Networking Platform v2.5
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2017 Rehan Adil. All rights reserved.
**/

$ajax = true;
require_once('assets/includes/core.php');
$isLogged = isLogged();

foreach ($_GET as $key => $value)
{
    $themeData['get_' . $escapeObj->stringEscape(strtolower($key))] = $escapeObj->stringEscape($value);
}

require_once('index/nav.php');
require_once('index/footer.php');
require_once('index/page.php');

$data = array(
    "status" => 200,
    "title" => $themeData['page_title'],
    "html" => $themeData['page_content']
);
if (isset($ajaxUrl)) $data['url'] = $ajaxUrl;

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();