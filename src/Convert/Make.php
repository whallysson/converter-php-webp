<?php

namespace CodeBlog\ToWebP\Convert;

/**
 * Class Make
 *
 * @author Whallysson Avelino <https://github.com/whallysson>
 * @package CodeBlog\ToWebP\Convert
 */
class Make
{

    /** @var string */
    protected $path;

    /** @var string */
    protected $name;

    /**
     * Make constructor.
     * @param string $uploadDir
     * @param string $fileTypeDir
     * @throws \Exception
     */
    public function __construct(string $uploadDir, string $fileTypeDir)
    {
        $this->dir($uploadDir);
        $this->dir("{$uploadDir}/{$fileTypeDir}");
        $this->path("{$uploadDir}/{$fileTypeDir}");
    }

    /**
     * @param string $string
     * @return string
     */
    protected function name(string $string)
    {
        $info = pathinfo(filter_var(mb_strtolower($string), FILTER_SANITIZE_STRIPPED));

        $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª¹²³';
        $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                    ';
        $name = str_replace(["-----", "----", "---", "--"], "-",
            str_replace(" ", "-", trim(strtr(utf8_decode($info['filename']), utf8_decode($formats), $replace))));

        $this->name = "{$name}.{$info['extension']}";

        return $this->name;
    }

    /**
     * @param string $dir
     * @param int $mode
     * @throws \Exception
     */
    protected function dir(string $dir, int $mode = 0775)
    {
        if (!file_exists($dir)) {

            // Trying to create the given folder
            if (!mkdir($dir, $mode, true)) {
                throw new \Exception('Failed creating folder: ' . $dir);
            }

            chmod($dir, $mode);
        }

        // Checks if there's a file in $filePath & if writing permissions are correct
        if (file_exists($dir) && !is_writable($dir)) {
            throw new \Exception('Cannot overwrite ' . basename($dir) . ' - check file permissions.');
        }

        return;
    }

    /**
     * @param string $path
     */
    protected function path(string $path)
    {
        list($yearPath, $mothPath) = explode("/", date("Y/m"));

        $this->dir("{$path}/{$yearPath}");
        $this->dir("{$path}/{$yearPath}/{$mothPath}");
        $this->path = "{$path}/{$yearPath}/{$mothPath}";
    }

}
