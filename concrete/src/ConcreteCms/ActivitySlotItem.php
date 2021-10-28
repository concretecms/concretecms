<?php
namespace Concrete\Core\ConcreteCms;

use Concrete\Core\Http\Service\Json;

class ActivitySlotItem
{
    protected $content;

    /**
     * @param $content
     */
    public function __construct($content = null)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $response Parses a JSON response that looks similar to below
     * <code>
     * {
     *     'slots': {
     *          'slotKey1': 'content',
     *          'slotKey2': 'other content',
     *          ...
     *      }
     * }
     * </code>
     *
     * @return ActivitySlotItem[] Returns an associative array of ActivitySlotItem
     */
    public function parseResponse($response)
    {
        $slots = array();
        try {
            $json = new Json();
            $obj = $json->decode($response);
            if (is_object($obj)) {
                if (is_object($obj->slots)) {
                    foreach ($obj->slots as $key => $content) {
                        $cn = new self($content);
                        $slots[$key] = $cn;
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $slots;
    }
}
