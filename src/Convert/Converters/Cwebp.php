<?php

namespace CodeBlog\ToWebP\Convert\Converters;

/**
 * Class Cwebp
 *
 * @author Whallysson Avelino <https://github.com/whallysson>
 * @package CodeBlog\ToWebP\Convert\Converters
 */

use CodeBlog\ToWebP\AbstractConverter;

class Cwebp extends AbstractConverter
{
    /**
     * System paths to look for cwebp binary
     *
     * @var array
     */
    private $defaultPaths = [
        '/usr/bin/cwebp',
        '/usr/local/bin/cwebp',
        '/usr/gnu/bin/cwebp',
        '/usr/syno/bin/cwebp',
    ];

    /**
     * OS-specific binaries included in this library
     *
     * @var mixed
     */
    private $binary = [
        'WinNT' => ['cwebp.exe', '49e9cb98db30bfa27936933e6fd94d407e0386802cb192800d9fd824f6476873'],
        'Darwin' => ['cwebp-mac12', 'a06a3ee436e375c89dbc1b0b2e8bd7729a55139ae072ed3f7bd2e07de0ebb379'],
        'SunOS' => ['cwebp-sol', '1febaffbb18e52dc2c524cda9eefd00c6db95bc388732868999c0f48deb73b4f'],
        'FreeBSD' => ['cwebp-fbsd', 'e5cbea11c97fadffe221fdf57c093c19af2737e4bbd2cb3cd5e908de64286573'],
        'Linux' => ['cwebp-linux', '916623e5e9183237c851374d969aebdb96e0edc0692ab7937b95ea67dc3b2568'],
    ][PHP_OS];

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function checkRequirements()
    {
        if (!function_exists('exec')) {
            throw new \Exception('exec() is not enabled.');
        }

        return true;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function setUpBinaries()
    {
        // Removes system paths if the corresponding binary doesn't exist
        $binaries = array_filter($this->defaultPaths, function($binary) {
            return file_exists($binary);
        });

        $binaryFile = __DIR__ . '/Binaries/' . $this->binary[0];

        // Throws an exception if binary file does not exist
        if (!file_exists($binaryFile)) {
            throw new \Exception('Operating system is currently not supported: ' . PHP_OS);
        }

        // File exists, now generate its hash
        $binaryHash = hash_file('sha256', $binaryFile);

        // Throws an exception if binary file checksum & deposited checksum do not match
        if ($binaryHash != $this->binary[1]) {
            throw new \Exception('Binary checksum is invalid.');
        }

        $binaries[] = $binaryFile;

        return $binaries;
    }

    /**
     * Checks if 'Nice' is available
     *
     * @return bool
     */
    public function hasNiceSupport()
    {
        exec("nice 2>&1", $niceOutput);

        if (is_array($niceOutput) && isset($niceOutput[0])) {
            if (preg_match('/usage/', $niceOutput[0]) || (preg_match('/^\d+$/', $niceOutput[0]))) {
                /*
                 * Nice is available - default niceness (+10)
                 * https://www.lifewire.com/uses-of-commands-nice-renice-2201087
                 * https://www.computerhope.com/unix/unice.htm
                 */

                return true;
            }

            return false;
        }
    }

    /**
     * @return bool|mixed
     */
    public function convert()
    {
        try {
            $this->checkRequirements();

            // Preparing array holding possible binaries
            $binaries = $this->setUpBinaries();
        } catch (\Exception $e) {
            return false; // TODO: `throw` custom \Exception $e & handle it smoothly on top-level.
        }

        /*
         * Preparing options
         */

        // lossless PNG conversion
        $lossless = (
        $this->extension == 'png'
            ? '-lossless'
            : ''
        );

        // Built-in method option
        $method = (
        defined('PHPWEBP_CWEBP_METHOD')
            ? '-m ' . PHPWEBP_CWEBP_METHOD
            : '-m 6'
        );

        // Metadata (all, exif, icc, xmp or none (default))
        // Comma-separated list of existing metadata to copy from input to output
        $metadata = (
        $this->strip
            ? '-metadata none'
            : '-metadata all'
        );

        // Built-in low memory option
        if (!defined('PHPWEBP_CWEBP_LOW_MEMORY')) {
            $lowMemory = '-low_memory';
        } else {
            $lowMemory = (
            PHPWEBP_CWEBP_LOW_MEMORY
                ? '-low_memory'
                : ''
            );
        }

        $optionsArray = [
            $lossless = $lossless,
            $quality = '-q ' . $this->quality,
            $method = $method,
            $metadata = $metadata,
            $lowMemory = $lowMemory,
            $input = $this->escapeFilename($this->source),
            $output = '-o ' . $this->escapeFilename($this->destination),
            $stderrRedirect = '2>&1',
        ];

        $options = implode(' ', array_filter($optionsArray));

        $nice = (
        $this->hasNiceSupport()
            ? 'nice'
            : ''
        );

        $success = false;

        // Try all paths
        foreach ($binaries as $index => $binary) {
            $command = $nice . ' ' . $binary . ' ' . $options;
            exec($command, $result, $returnCode);

            if ($returnCode == 0) { // Everything okay!
                // cwebp sets file permissions to 664 but instead ..
                // .. $destination's parent folder's permissions should be used (except executable bits)
                $destinationParent = dirname($this->destination);
                $fileStatistics = stat($destinationParent);

                // Apply same permissions as parent folder but strip off the executable bits
                $permissions = $fileStatistics['mode'] & 0000666;
                chmod($this->destination, $permissions);

                $success = true;
                break;
            }
        }

        return $success;
    }
}
