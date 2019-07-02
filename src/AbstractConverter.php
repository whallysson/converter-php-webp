<?php

namespace CodeBlog\ToWebP;

/**
 * Class AbstractConverter
 *
 * @author Whallysson Avelino <https://github.com/whallysson>
 * @package CodeBlog\ToWebP
 */

abstract class AbstractConverter
{

    /** @var */
    protected $source;

    /** @var */
    protected $destination;

    /** @var int */
    protected $quality;

    /** @var bool */
    protected $strip;

    /** @var string */
    protected $extension;

    /**
     * AbstractConverter constructor.
     * @param $source
     * @param $destination
     * @param int $quality
     * @param bool $stripMetadata
     */
    public function __construct($source, $destination, $quality = 85, $stripMetadata = false)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->quality = $quality;
        $this->strip = $stripMetadata;

        $this->extension = $this->getExtension($source);
    }

    /**
     * Forces every converter to implement the following functions:
     * `checkRequirements()` - checks if converter's requirements are met
     * `convert()` - converting given image to WebP
     *
     * @return mixed
     */
    abstract public function checkRequirements();

    /**
     * @return mixed
     */
    abstract public function convert();

    /**
     * Returns given file's extension
     *
     * @param string $filePath
     *
     * @return string
     */
    public function getExtension($filePath)
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        return strtolower($fileExtension);
    }

    /**
     * Returns escaped version of string
     *
     * @param $string
     * @return mixed|string|string[]|null
     */
    public function escapeFilename($string)
    {
        // Escaping whitespaces & quotes
        $string = preg_replace('/\s/', '\\ ', $string);
        $string = filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES);

        // Stripping control characters
        // see https://stackoverflow.com/questions/12769462/filter-flag-strip-low-vs-filter-flag-strip-high
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

        return $string;
    }

}
