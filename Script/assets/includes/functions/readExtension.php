<?php
function readExtension($n)
{
	return strtolower(substr($n, strrpos($n, '.') + 1, strlen($n) - strrpos($n, '.')));
}
