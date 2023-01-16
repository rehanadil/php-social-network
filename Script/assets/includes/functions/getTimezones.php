<?php
function getTimezones()
{
	return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
}