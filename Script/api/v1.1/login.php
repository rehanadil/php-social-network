<?php
$login = appLogin($_POST);

if ($login)
{
	$data = $login;
}