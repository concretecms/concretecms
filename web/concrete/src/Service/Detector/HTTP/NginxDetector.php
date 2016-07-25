<?php
namespace Concrete\Core\Service\Detector\HTTP;

use Concrete\Core\Http\Request;
use Concrete\Core\Service\Detector\DetectorInterface;

class NginxDetector implements DetectorInterface
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
        if ($result === null && $this->request->server->has('SERVER_SOFTWARE')) {
            $result = $this->detectFromServer($this->request->server->get('SERVER_SOFTWARE'));
        }
        if ($result === null && function_exists('apache_get_version')) {
            $result = $this->detectFromSPL(@apache_get_version());
        }
        if ($result === null) {
            ob_start();
            phpinfo(INFO_MODULES);
            $info = ob_get_contents();
            ob_end_clean();
            $result = $this->detectFromPHPInfo($info);
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
        if (is_string($value) && preg_match('/\bnginx\/(\d+(\.\d+)+)/i', $value, $m)) {
            $result = $m[1];
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
        if (is_string($value) && preg_match('/\bnginx\/(\d+(\.\d+)+)/i', $value, $m)) {
            $result = $m[1];
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
        if (is_string($value) && preg_match('/\bnginx\/(\d+(\.\d+)+)/i', $value, $m)) {
            $result = $m[1];
        }

        return $result;
    }
}
