<?php
namespace Concrete\Core\Service\Detector\HTTP;

use Concrete\Core\Http\Request;
use Concrete\Core\Service\Detector\DetectorInterface;

class ApacheDetector implements DetectorInterface
{
    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * Class constructor.
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Service\Detector\DetectorInterface::detect()
     */
    public function detect()
    {
        $result = null;
        if (($result === null || $result === '') && $this->request->server->has('SERVER_SOFTWARE')) {
            $version = $this->detectFromServer($this->request->server->get('SERVER_SOFTWARE'));
            if ($version !== null) {
                $result = $version;
            }
        }
        if (($result === null || $result === '') && function_exists('apache_get_version')) {
            $version = $this->detectFromSPL(@apache_get_version());
            if ($version !== null) {
                $result = $version;
            }
        }
        if ($result === null || $result === '') {
            ob_start();
            phpinfo(INFO_MODULES);
            $info = ob_get_contents();
            ob_end_clean();
            $result = $this->detectFromPHPInfo($info);
        }
        if (($result === null || $result === '')) {
            $version = $this->detectFromSapiName(PHP_SAPI);
            if ($version !== null) {
                $result = $version;
            }
        }

        return $result;
    }

    /**
     * Detect from the SERVER_SOFTWARE key of the superglobal server array.
     *
     * @param string $value
     *
     * @return null|string
     */
    private function detectFromServer($value)
    {
        $result = null;
        if (is_string($value)) {
            if (preg_match('/\bApache\/(\d+(\.\d+)+)/i', $value, $m)) {
                $result = $m[1];
            } elseif ($value === 'Apache') {
                $result = '';
            }
        }

        return $result;
    }

    /**
     * Detect using the result of the SPL apache_get_version().
     *
     * @param string $value
     *
     * @return null|string
     */
    private function detectFromSPL($value)
    {
        $result = null;
        if (is_string($value)) {
            if (preg_match('/\bApache\/(\d+(\.\d+)+)/i', $value, $m)) {
                $result = $m[1];
            } elseif ($value === 'Apache') {
                $result = '';
            }
        }

        return $result;
    }

    /**
     * Detect using PHPInfo.
     *
     * @param string $value
     *
     * @return null|string
     */
    private function detectFromPHPInfo($value)
    {
        $result = null;
        if (is_string($value) && preg_match('/\bApache\/(\d+(\.\d+)+)/i', $value, $m)) {
            $result = $m[1];
        }

        return $result;
    }

    /**
     * Detect using PHP_SAPI/php_sapi_name.
     *
     * @param string $value
     *
     * @return null|string
     */
    private function detectFromSapiName($sapiName)
    {
        $result = null;
        if (is_string($sapiName) && preg_match('/^apache(\d)handler$/', $sapiName, $m)) {
            $result = $m[1];
        }

        return $result;
    }
}
