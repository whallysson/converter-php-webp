<?php

namespace CodeBlog\ToWebP\Convert\Converters;

use CodeBlog\ToWebP\AbstractConverter;
use Exception;

/**
 * Class Imagick
 *
 * Converts an image to WebP via ImageMagick
 *
 * @author Whallysson Avelino <https://github.com/whallysson>
 * @package CodeBlog\ToWebP\Convert\Converters
 */
class Imagick extends AbstractConverter
{
    /**
     * @return bool|mixed
     * @throws Exception
     */
    public function checkRequirements()
    {
        if (!extension_loaded('imagick')) {
            throw new Exception('Required iMagick extension is not available.');
        }

        if (!class_exists('Imagick')) {
            throw new Exception('iMagick is installed but cannot handle source file.');
        }

        return true;
    }

    /**
     * @return bool|mixed
     */
    public function convert()
    {
        try {
            $this->checkRequirements();
            $im = new \Imagick($this->source);
            // Throws an exception if iMagick does not support WebP conversion
            if (!in_array('WEBP', $im->queryFormats())) {
                throw new Exception('iMagick was compiled without WebP support.');
            }
            $im->setImageFormat('WEBP');
        } catch (Exception $e) {
            return false; // TODO: `throw` custom \Exception $e & handle it smoothly on top-level.
        }

        // Apply losless compression for PNG images
        switch ($this->extension) {
            case 'png':
                $im->setOption('webp:lossless', 'true');
                break;
            default:
                break;
        }

        return $this->toImage($im);
    }

    /**
     * @param $image
     * @return bool
     */
    private function toImage($image)
    {
        /*
         * More about iMagick's WebP options:
         * http://www.imagemagick.org/script/webp.php
         * https://stackoverflow.com/questions/37711492/imagemagick-specific-webp-calls-in-php
         */
        $image->setOption('webp:method', '6');
        $image->setOption('webp:low-memory', 'true');
        $image->setImageCompressionQuality($this->quality);

        // TODO: Check out other iMagick methods, see http://php.net/manual/de/imagick.writeimage.php#114714
        // 1. file_put_contents($destination, $im)
        // 2. $im->writeImage($destination)
        $wh = fopen($this->destination, 'wb');

        $success = $wh !== false ? $image->writeImageFile($wh) : false;

        // TODO: Check whether Imagick::writeImageFile returns false if conversion was unsuccessful
        if (!$success) { return false; }

        return true;
    }
}
