<?php
function Sk_getArrayTheme($Array, $startPrefix='')
{
	global $themeData;
	foreach ($Array as $k1 => $v1)
	{
		$k1 = strtolower($k1);
		if (is_array($v1))
		{
			Sk_getArrayTheme($v1, $startPrefix . '_' . $k1);
		}
		else
		{
			$themeData[$startPrefix . '_' . $k1] = $v1;
		}
	}
}