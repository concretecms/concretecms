<?php

namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;
use Exception;
use Throwable;
use UnexpectedValueException;

/**
 * An inspector to process FLV (Flash Video) files.
 */
class FlvInspector extends Inspector
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Type\Inspector\Inspector::inspect()
     */
    public function inspect(Version $fv)
    {
        $metadata = $this->getFlvMetadata($fv);
        if ($metadata !== null) {
            $attributeCategory = $fv->getObjectAttributeCategory();
            if (isset($metadata['duration']) && is_float($metadata['duration']) && $metadata['duration'] > 0) {
                $atDuration = $attributeCategory->getAttributeKeyByHandle('duration');
                if ($atDuration !== null) {
                    $fv->setAttribute($atDuration, $metadata['duration']);
                }
            }
            if (isset($metadata['width']) && is_float($metadata['width'])) {
                $width = (int) round($metadata['width']);
                if ($width > 0) {
                    $atWidth = $attributeCategory->getAttributeKeyByHandle('width');
                    if ($atWidth !== null) {
                        $fv->setAttribute($atWidth, $width);
                    }
                }
            }
            if (isset($metadata['height']) && is_float($metadata['height'])) {
                $height = (int) round($metadata['height']);
                if ($height > 0) {
                    $atHeight = $attributeCategory->getAttributeKeyByHandle('height');
                    if ($atHeight !== null) {
                        $fv->setAttribute($atHeight, $height);
                    }
                }
            }
        }
    }

    /**
     * @param Version $fv
     *
     * @return array|null
     *
     * @see http://www.adobe.com/devnet/f4v.html
     */
    public function getFlvMetadata(Version $fv)
    {
        $result = null;
        $fp = $this->getStream($fv);
        if ($fp !== null) {
            try {
                $headerData = $this->readFlvHeader($fp);
                if ($headerData !== null) {
                    $tagOffset = $headerData['dataOffset'];
                    for ($tagIndex = 0; ; ++$tagIndex) {
                        $tagData = $this->readFlvTag($fp, $tagOffset);
                        if ($tagData === null) {
                            break;
                        }
                        switch ($tagData['tagType']) {
                            case 18: // Script data
                                if ($tagData['filter'] === 0) { // Not encrypted
                                    $data = @fread($fp, $tagData['dataSize']);
                                    if ($data !== false && isset($data[$tagData['dataSize'] - 1])) {
                                        $scriptBody = $this->extractScriptTagBody($data);
                                        if ($scriptBody !== null) {
                                            list($bodyName, $bodyData) = $scriptBody;
                                            if ($bodyName === 'onMetaData') {
                                                $result = $bodyData;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                        $tagOffset += $dataSize + 15;
                        break; // Let's just parse the first tag
                    }
                }
            } catch (Exception $x) {
                $result = null;
            } catch (Throwable $x) {
                $result = null;
            }
            if (is_resource($fp)) {
                @fclose($fp);
            }
        }

        return $result;
    }

    /**
     * @param Version $version
     * @param Version $fv
     *
     * @return resource|null
     */
    private function getStream(Version $fv)
    {
        try {
            return $fv->getFileResource()->readStream() ?: null;
        } catch (Exception $x) {
            return null;
        } catch (Throwable $x) {
            return null;
        }
    }

    /**
     * @param resource $fp
     *
     * @throws UnexpectedValueException
     *
     * @return array|null
     */
    private function readFlvHeader($fp)
    {
        $result = null;
        $flvHeaderChunk = @fread($fp, 9); // 3 bytes signature + 1 byte version + 1 byte flags + 4 bytes data offset
        if ($flvHeaderChunk !== false && isset($flvHeaderChunk[8])) {
            if (substr($flvHeaderChunk, 0, 3) === 'FLV') { // Signature ok
                $version = $this->parseUI8($flvHeaderChunk[3]);
                if ($version === 1) { // Version ok
                    $flags = $this->parseUI8($flvHeaderChunk[4]); // Bit 0x01 set: has video; bit 0x04: has audio
                    $dataOffset = $this->parseUI32(substr($flvHeaderChunk, 5, 4));
                    $result = [
                        'version' => $version,
                        'flags' => $flags,
                        'dataOffset' => $dataOffset,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param resource $fp
     * @param int $tagOffset
     *
     * @throws UnexpectedValueException
     *
     * @return array|null
     */
    private function readFlvTag($fp, $tagOffset)
    {
        $result = null;
        $seeked = @fseek($fp, $tagOffset + 4); // +4 to skip PreviousTagSize
        if ($seeked === 0) {
            $tagHeaderChunk = @fread($fp, 11); // (2 bits reserved + 1 bit filter + 5 bits TagType) + 3 bytes DataSize + 3 bytes Timestamp + 1 byte TimestampExtended + 3 bytes StreamID
            if ($tagHeaderChunk !== false && isset($tagHeaderChunk[10])) {
                $byte = $this->parseUI8($tagHeaderChunk[0]);
                $result = [
                    'filter' => ($byte >> 5) & 0x01,
                    'tagType' => $byte & 0x1F,
                    'dataSize' => $this->parseUI24(substr($tagHeaderChunk, 1, 3)),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return int
     */
    private function parseUI8($data)
    {
        if (!isset($data[0])) {
            throw new UnexpectedValueException();
        }

        return ord($data);
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return int
     */
    private function parseUI16($data)
    {
        if (!isset($data[1])) {
            throw new UnexpectedValueException();
        }
        $unpacked = unpack('n', $data);

        return array_shift($unpacked);
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return int
     */
    private function parseUI24($data)
    {
        if (!isset($data[2])) {
            throw new UnexpectedValueException();
        }
        $unpacked = unpack('N', "\x00" . $data);

        return array_shift($unpacked);
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return int
     */
    private function parseUI32($data)
    {
        if (!isset($data[3])) {
            throw new UnexpectedValueException();
        }
        $unpacked = unpack('N', $data);

        return array_shift($unpacked);
    }

    /**
     * IEEE 754.
     *
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return float
     */
    private function parseDouble($data)
    {
        if (!isset($data[7])) {
            throw new UnexpectedValueException();
        }
        $unpacked = unpack('E', $data);

        return array_shift($unpacked);
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return array|null
     */
    private function extractScriptTagBody(&$data)
    {
        $result = null;
        $name = $this->extractScriptDataValue($data);
        if (is_string($name)) {
            $value = $this->extractScriptDataValue($data);
            if (is_array($value)) {
                $result = [$name, $value];
            }
        }

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws \UnexpectedValueException
     *
     * @return mixed
     */
    private function extractScriptDataValue(&$data)
    {
        $result = null;
        if (isset($data[0])) {
            $type = $this->parseUI8($data[0]);
            $data = substr($data, 1);
            switch ($type) {
                case 0: // Number
                    $result = $this->extractScriptDataValue_Double($data);
                    break;
                case 1: // Boolean
                    $result = $this->extractScriptDataValue_Boolean($data);
                    break;
                case 2: // String
                    $result = $this->extractScriptDataValue_String($data);
                    break;
                case 7: // Reference
                    $result = $this->extractScriptDataValue_UI16($data);
                    break;
                case 8: // ECMA array
                    $result = $this->extractScriptDataValue_ECMAArray($data);
                case 9: // Object end marker
                    $result = null;
                    break;
                //case 3: // Object
                //case 4: // MovieClip (reserved, not supported)
                //case 5: // Null
                //case 6: // Undefined
                //case 10: // Strict array
                //case 11: // Date
                //case 12: // Long string
                default:
                    throw new UnexpectedValueException();
            }
        }

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return int
     */
    private function extractScriptDataValue_UI16(&$data)
    {
        $result = $this->parseUI16(substr($data, 0, 2));
        $data = substr($data, 2);

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return float
     */
    private function extractScriptDataValue_Double(&$data)
    {
        $result = $this->parseDouble(substr($data, 0, 8));
        $data = substr($data, 8);

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return bool
     */
    private function extractScriptDataValue_Boolean(&$data)
    {
        $result = $this->parseUI8($data[0]) !== 0;
        $data = substr($data, 1);

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return string
     */
    private function extractScriptDataValue_String(&$data)
    {
        $stringLength = $this->parseUI16(substr($data, 0, 2));
        $result = substr($data, 2, $stringLength);
        $data = substr($data, 2 + $stringLength);

        return $result;
    }

    /**
     * @param string $data
     *
     * @throws UnexpectedValueException
     *
     * @return array
     */
    private function extractScriptDataValue_ECMAArray(&$data)
    {
        $result = [];
        if (isset($data[4])) {
            $approximateLength = $this->parseUI32(substr($data, 0, 4));
            $data = substr($data, 4);
            while ($data !== '') {
                $propertyName = $this->extractScriptDataValue_String($data);
                $propertyValue = $this->extractScriptDataValue($data);
                if ($propertyName === '' && $propertyValue === null) {
                    // Object end marker
                    break;
                }
                $result[$propertyName] = $propertyValue;
            }
        }

        return $result;
    }
}
