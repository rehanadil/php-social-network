<?php
function footer_call_mediaElement_videoUpload ()
{
	return '<script>
	$(".singleStoryJar").find("video").css("max-width", $(".singleStoryJar").width());
	$(window).resize(function(){
		$(".singleStoryJar").find("video").css("max-width", $(".singleStoryJar").width());
	});
	</script>';
}
\SocialKit\Addons::register('body_end_add_content', 'footer_call_mediaElement_videoUpload');
