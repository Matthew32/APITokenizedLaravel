<?php

namespace App\Utils;


class ImageManipulator
{
    /**
     * Create identicon from the data passed  in the parameter
     * @param $data
     * @param string $filename
     * @return false|resource
     */
    public static function createIdenticon($data, $filename = "identicon.jpg")
    {
// Convert data to MD5
        $hash = md5($data);
        // Get color from first 6 characters
        $color = substr($hash, 0, 6);
        // Create an array to store our boolean "pixel" values
        $pixels = array();

        // Make it a 5x5 multidimensional array
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $pixels[$i][$j] = hexdec(substr($hash, ($i * 5) + $j + 6, 1)) % 2 === 0;
            }
        }

        // Create image
        $image = imagecreatetruecolor(125, 250);
        // Allocate the primary color. The hex code we assigned earlier needs to be decoded to RGB
        $color = imagecolorallocate($image, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
        // And a background color
        $bg = imagecolorallocate($image, 238, 238, 238);

        // Color the pixels
        for ($k = 0; $k < count($pixels); $k++) {
            for ($l = 0; $l < count($pixels[$k]); $l++) {
                // Default pixel color is the background color
                $pixelColor = $bg;

                // If the value in the $pixels array is true, make the pixel color the primary color
                if ($pixels[$k][$l]) {
                    $pixelColor = $color;
                }

                // Color the pixel. In a 250x250px image with a 5x5 grid of "pixels", each "pixel" is 50x50px
                imagefilledrectangle($image, $k * 50, $l * 50, ($k + 1) * 50, ($l + 1) * 50, $pixelColor);
            }
        }
        imagepng($image, $filename);
        imagedestroy($image);

        //flip images to make the mirror
        $fliped = imagecreatefrompng($filename);
        imageflip($fliped, IMG_FLIP_HORIZONTAL);

        $image = imagecreatefrompng($filename);

        //create final image to put  the image
        $ImageTotal = imagecreatetruecolor(250, 250);

        imagecopy($ImageTotal, $image, 0, 0, 0, 0, 125, 250);

        imagecopy($ImageTotal, $fliped, 125, 0, 0, 0, 125, 250);


        imagejpeg($ImageTotal, $filename, 75);
        return $ImageTotal;
    }
}
