<?php
function getCountries($ci=0)
{
	global $conn;
	$ci = (int) $ci;
	$array = array();
	$query = ($ci == 0) ? $conn->query("SELECT * FROM " . DB_COUNTRIES . " ORDER BY name ASC") : $conn->query("SELECT * FROM " . DB_COUNTRIES . " WHERE id=$ci");

	if ($ci > 0) return $query->num_rows;

	while ($co = $query->fetch_array(MYSQLI_ASSOC))
	{
		$array[$co['id']] = $co['name'];
	}

	return $array;
}