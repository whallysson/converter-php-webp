<?php


namespace CodeBlog\ToWebP;

/**
 * Class ToWebP
 *
 * Converts JPEG & PNG to WebP with PHP
 *
 * @author Whallysson Avelino <https://github.com/whallysson>
 * @package CodeBlog\ToWebP
 */

use CodeBlog\ToWebP\Convert\Make;
use Exception;

class ToWebP extends Make
{

    /** @var array */
    private $converters = ['imagick', 'cwebp', 'gd'];

    /** @var array */
    protected $preferredConverters = [];

    /** @var array */
    protected $allowedExtensions = ['jpg', 'jpeg', 'png'];

    /** @var string */
    public $image_webp;

    /** @var string */
    public $image_original;


    /**
     * ToWebP constructor.
     * @param string $uploadDir
     * @param string $fileTypeDir
     * @param null $converters
     * @throws Exception
     */
    public function __construct(string $uploadDir, string $fileTypeDir, $converters = null)
    {
        parent::__construct($uploadDir, $fileTypeDir);

        if ($converters != null) {
            $this->setConverters($converters);
        }
    }

    /**
     * Converts image to WebP
     *
     * @param string $source Path of input image
     * @param string $destination Path of output image
     * @param integer $quality Image compression quality (ranging from 0 to 100)
     * @param boolean $stripMetadata Whether to strip metadata
     *
     * @return boolean
     */
    public function convert(string $source, string $name, int $quality = 85, bool $stripMetadata = true)
    {
        try {
            $this->image_original = $source;

            $this->isValidTarget($source);
            $this->isAllowedExtension($source);

            $success = false;

            // set local and name
            $this->name($name);

            if (!file_exists("{$this->path}/{$this->name}")) {

                // .. and iterates over them
                foreach ($this->setUpConverters() as $currentConverter) {
                    $converterName = ucfirst(strtolower($currentConverter));
                    $className = 'CodeBlog\\ToWebP\\Convert\\Converters\\' . $converterName;

                    if (!class_exists($className)) {
                        continue;
                    }

                    $converter = new $className(
                        $source,
                        "{$this->path}/{$this->name}",
                        $quality,
                        $stripMetadata
                    );

                    if (!$converter instanceof AbstractConverter || !is_callable([$converter, 'convert'])) {
                        continue;
                    }

                    $conversion = call_user_func([$converter, 'convert']);

                    if ($conversion) {
                        $success = true;
                        $this->setConverters($currentConverter);

                        break;
                    }
                }
            }

            $this->image_webp = "{$this->path}/{$this->name}";

            return $success;

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }


    /**
     * Returns the picture picture formatted with the original image is the source with the type webp
     *
     * @param array|null $options
     * @return string
     */
    public function picture(array $options = null)
    {
        $picture = null;
        $img = null;

        if (!empty($options['picture'])) {
            foreach ($options['picture'] as $key => $val) {
                $picture .= " {$key}='{$val}'";
            }
        }

        if (!empty($options['img'])) {
            foreach ($options['img'] as $key => $val) {
                $img .= " {$key}='{$val}'";
            }
        }

        return "<picture{$picture}>
                    <source srcset='{$this->image_webp}' type='image/webp'>
                    <img src='{$this->image_original}'{$img} />
                </picture>";
    }


    /**
     * Checks whether provided file exists
     *
     * @param $filePath
     * @return bool
     * @throws Exception
     */
    private function isValidTarget($filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception('File or directory not found: ' . $filePath);
        }

        return true;
    }


    /**
     * @param $filePath
     * @return bool
     * @throws Exception
     */
    private function isAllowedExtension($filePath)
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array(strtolower($fileExtension), $this->allowedExtensions)) {
            throw new Exception('Unsupported file extension: ' . $fileExtension);
        }

        return true;
    }

    /**
     * Sets preferred converter(s)
     *
     * @param array|string $preferred
     */
    private function setConverters($preferred = [])
    {
        if (is_string($preferred)) {
            $this->preferredConverters = (array)$preferred;
            return;
        }
        $this->preferredConverters = $preferred;
    }

    /**
     * Gets preferred converter(s)
     *
     * @return array
     */
    public function getConverters()
    {
        return $this->preferredConverters;
    }

    /**
     * Sets up converters to be used during conversion
     *
     * @return array
     */
    private function setUpConverters()
    {
        // Returns available converters if no preferred converters are set
        if (empty($this->preferredConverters)) {
            return $this->converters;
        }

        // Returns preferred converters if set & remaining ones be skipped
        return $this->preferredConverters;
    }

}
