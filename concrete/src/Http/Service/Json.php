<?php
namespace Concrete\Core\Http\Service;
/*
 * This class isn't used any longer. If you need json_encode or json_decode just use native PHP versions.
 * @deprecated
 */
class Json
{

    /**
     * Decodes a JSON string into a php variable
     * @param string $string
     * @param bool $assoc [default: false] When true, returned objects will be converted into associative arrays, when false they'll be converted into stdClass instances.
     * @return mixed
     */
    public function decode($string, $assoc = false)
    {
        return json_decode($string, $assoc);
    }


    /**
     * Encodes a data structure into a JSON string
     * @param mixed $mixed
     * @return string
     */
    public function encode($mixed)
    {
        return json_encode($mixed);
    }


}
