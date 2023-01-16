<?php
if (isset($_GET['rid'])
	&& isset($_GET['status']))
{
	$rid = (int) $_GET['rid'];
	$status = (int) $_GET['status'];

	$reportQuery = $conn->query("SELECT post_id FROM " . DB_REPORTS . " WHERE id=$rid");

	if ($reportQuery->num_rows == 1)
	{
		$conn->query("UPDATE " . DB_REPORTS . " SET status=$status WHERE id=$rid");
		$report = $reportQuery->fetch_array(MYSQLI_ASSOC);
		if ($status === 2) $conn->query("DELETE FROM " . DB_POSTS . " WHERE post_id=" . $report['post_id']);
	}
}