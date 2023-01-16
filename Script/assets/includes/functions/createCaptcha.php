<?php
/* Create captcha */
function createCaptcha()
{
    $image = '';
    $image = @imagecreatetruecolor(80, 30);
    $background_color = @imagecolorallocate($image, 78, 86, 101);
    $text_color = @imagecolorallocate($image, 255, 255, 255);
    $pixel_color = @imagecolorallocate($image, 60, 75, 114);
    @imagefilledrectangle($image, 0, 0, 80, 30, $background_color);
    
    for ($i = 0; $i < 1000; $i++)
    {
        @imagesetpixel($image, rand() % 80, rand() % 30, $pixel_color);
    }
    
    $key = generateKey(6, 6, false, false, true);
    $_SESSION['captcha_key'] = $key;
    @imagestring($image, 7, 13, 8, $key, $text_color);
    $images = glob('photos/captcha_*.png');
    
    if (is_array($images))
    {
        foreach ($images as $image_to_delete)
        {
            @unlink($image_to_delete);
        }
    }
    
    $image_url = 'photos/captcha_' . time() . '_' . mt_rand(1, 9999) . '.png';
    @imagepng($image, $image_url);
    
    $get = array(
        'image' => $image_url
    );
    return $get;
}