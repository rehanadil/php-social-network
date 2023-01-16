<?php
$footerTags = "";

if ($isLogged)
{
	/* Data Storage */
	$dataStorage = '<div id="dataStorage" style="display:none;">';
		/* Skemojis */
		$dataStorage .= '<div id="Skemojis">' . getSkemojiHtml() . '</div>';
		/* ------ */
	$dataStorage .= '</div>
	<script>
	function Sk_getFromStorage(fs)
	{
		if ($("#dataStorage").find("#" + fs).length === 1)
		{
			return $("#dataStorage").find("#" + fs).html();
		}
	}
	</script>';
	$footerTags = $dataStorage;
	/* ------ */
}

$themeData['footer_tags'] = \SocialKit\Addons::invoke(array(
	'key' => 'footer_tags_add_content',
	'return' => 'string',
	'type' => 'APPEND',
	'content' => array(
		'data' => $footerTags
	)
));