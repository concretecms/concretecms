<?php

namespace Concrete\Core\Http;

class RequestMediaTypeParser
{
    /**
     * Optional white space characters.
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.3
     *
     * @var string
     */
    const OWS_CHARS = " \t";

    /**
     * Parameter-separator character.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.2
     *
     * @var string
     */
    const PARAMETER_SEPARATOR_CHAR = ';';

    /**
     * List-separator character.
     *
     * @see https://tools.ietf.org/html/rfc7230#section-1.2
     *
     * @var string
     */
    const LIST_SEPARATOR_CHAR = ',';

    /**
     * Regular expression chunk: token.
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.6
     *
     * @var string
     */
    const RX_TOKEN = '[\w\-\'!#$%&*+.^`|~]+';

    /**
     * Regular expression chunk: quoted string.
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.6
     *
     * @var string
     */
    const RX_QUOTED_STRING = '"[^"]*"';

    /**
     * Regular expression chunk: type.
     *
     * @see https://tools.ietf.org/html/rfc7231#appendix-D
     *
     * @var string
     */
    const RX_TYPE = self::RX_TOKEN;

    /**
     * Regular expression chunk: sub-type.
     *
     * @see https://tools.ietf.org/html/rfc7231#appendix-D
     *
     * @var string
     */
    const RX_SUBTYPE = self::RX_TOKEN;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * @var array|null
     */
    private $requestAcceptMap;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Check if the client signaled that it supports a media type.
     *
     * @param string|string[] $mediaType the media type to be checked (a string like 'text/html', or an array like ['text','html'])
     * @param float|null $minWeight the minimum weight of the found media type (from 0 to 1); if NULL we won't check it
     *
     * @return bool
     */
    public function isMediaTypeSupported($mediaType, $minWeight = null)
    {
        $data = $this->getMediaTypeData($mediaType);
        if ($minWeight === null) {
            return $data !== [];
        }
        $minWeight = (float) $minWeight;
        foreach ($data as $item) {
            if ($item['q'] >= $minWeight) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the data associated to a media type.
     *
     * @param string|string[] $mediaType the media type to be checked (a string like 'text/html', or an array like ['text','html'])
     *
     * @return array keys are the media type; values are the associated data, sorted by the preferred type (the 'q' parameter)
     */
    public function getMediaTypeData($mediaType)
    {
        $map = $this->getRequestAcceptMap();
        list($type, $subType) = $this->normalizeMediaType($mediaType);
        $result = [];
        foreach ($this->getMediaTypeAlternatives($type, $subType) as $alternative) {
            if (isset($map[$alternative])) {
                $result[$alternative] = $map[$alternative];
            }
        }

        return $result;
    }

    /**
     * Get the data extracted from the 'Accept' header.
     *
     * @return array keys are the media type; values are the associated data, sorted by the preferred type (the 'q' parameter)
     *
     * @example <code>['image/png' => ['q' => 1.0], 'image/*' => ['q' => 0.8]]
     */
    public function getRequestAcceptMap()
    {
        if ($this->requestAcceptMap === null) {
            $requestAccept = $this->getRequestAccept();
            $requestAcceptMap = $this->parseRequestAccept($requestAccept);
            $requestAcceptMap = $this->sortRequestAcceptMap($requestAcceptMap);
            $this->requestAcceptMap = $requestAcceptMap;
        }

        return $this->requestAcceptMap;
    }

    /**
     * Get the 'Accept' header of the request.
     *
     * @var string empty if not available
     */
    protected function getRequestAccept()
    {
        $accept = $this->request->headers->get('accept');
        if (!is_string($accept)) {
            return '';
        }

        return trim($accept);
    }

    /**
     * @param string $accept
     *
     * @return array
     */
    protected function parseRequestAccept($accept)
    {
        $result = [];
        $rxMediaType = $this->getTypeSubtypeRegularExpression();
        $rxParameterAndValue = $this->getParameterAndValueRegularExpression();
        $matches = null;
        for (; ;) {
            if ($accept === '') {
                break;
            }
            // Read type/subtype
            if (!preg_match("/^({$rxMediaType})/", $accept, $matches)) {
                // Malformed: return what we read so far
                return $result;
            }
            $foundMediaType = strtolower($matches[1]);
            $result[$foundMediaType] = ['q' => 1.0];
            $accept = ltrim(substr($accept, strlen($foundMediaType)), static::OWS_CHARS);
            // Read parameters (eg "; q=1")
            while ($accept !== '' && $accept[0] === static::PARAMETER_SEPARATOR_CHAR) {
                $accept = ltrim(substr($accept, 1), static::OWS_CHARS);
                if (!preg_match("/^{$rxParameterAndValue}/", $accept, $matches)) {
                    // Malformed: return what we read so far
                    return $result;
                }
                $accept = ltrim(substr($accept, strlen($matches[0])), static::OWS_CHARS);
                switch (strtolower($matches[1])) {
                    case 'q':
                        if (is_numeric($matches[2])) {
                            $result[$foundMediaType]['q'] = min(max((float) $matches[2], 0), 1);
                        }
                        break;
                    default:
                        $result[$foundMediaType][$matches[1]] = $matches[2];
                        break;
                }
            }
            if ($accept === '') {
                break;
            }
            // Goto next type/subtype
            if ($accept[0] !== static::LIST_SEPARATOR_CHAR) {
                // Malformed: return what we read so far
                return $result;
            }
            $accept = ltrim(substr($accept, 1), static::OWS_CHARS);
        }

        return $result;
    }

    /**
     * @param array $requestAcceptMap
     *
     * @return array
     */
    protected function sortRequestAcceptMap(array $requestAcceptMap)
    {
        uasort($requestAcceptMap, function ($a, $b) {
            if ($a['q'] > $b['q']) {
                return -1;
            }
            if ($a['q'] < $b['q']) {
                return 1;
            }

            return 0;
        });

        return $requestAcceptMap;
    }

    /**
     * @return string
     */
    protected function getTypeSubtypeRegularExpression()
    {
        $rxType = static::RX_TYPE;
        $rxSubType = static::RX_SUBTYPE;

        return "(?:{$rxType}|\*)\/(?:{$rxSubType}|\*)";
    }

    /**
     * @return string
     */
    protected function getParameterAndValueRegularExpression()
    {
        $rxToken = static::RX_TOKEN;
        $rxQuotedString = static::RX_QUOTED_STRING;

        return "({$rxToken})=({$rxToken}|{$rxQuotedString})";
    }

    /**
     * @param string|string[] $mediaType
     *
     * @return string[]
     */
    protected function normalizeMediaType($mediaType)
    {
        if (!is_array($mediaType)) {
            $mediaType = explode('/', (string) $mediaType, 2);
        }
        $type = (string) array_shift($mediaType);
        $type = $type === '' ? '*' : strtolower($type);
        $subType = (string) array_shift($mediaType);
        $subType = $subType === '' ? '*' : strtolower($subType);

        return [$type, $subType];
    }

    /**
     * @param string $type
     * @param string $subType
     *
     * @return string[]
     */
    protected function getMediaTypeAlternatives($type, $subType)
    {
        $result = ['*/*'];
        if ($type !== '*') {
            $result[] = "{$type}/*";
            if ($subType !== '*') {
                $result[] = "{$type}/{$subType}";
            }
        }

        return $result;
    }
}
