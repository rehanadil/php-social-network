<?php
function is_post_add_data($boo=1)
{
	if (is_bool($boo))
	{
		$_SESSION['post_add_data'] = $boo;
	}

	return $_SESSION['post_add_data'];
}