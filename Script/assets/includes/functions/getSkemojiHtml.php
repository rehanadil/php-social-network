<?php
function getSkemojiHtml()
{
	global $Skemoji;
	return '<i class="skemoji skemoji-' . implode('"></i><i class="skemoji skemoji-', $Skemoji) . '"></i>';
	/*$tharma = '';
	foreach ($Skemoji as $ik1 => $iv1) {
		$tharma .= '<i title="' . $iv1 . '" class="skemoji skemoji-' . $iv1 . '"></i><span class="deleteLater">' . $iv1 . '</span> ';
	}
	return $tharma;*/
}
$funcGetSkemojiHtml = "getSkemojiHtml";