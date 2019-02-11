<?php

namespace Concrete\Core\Logging\Processor;

/**
 * A processor for packing $_SERVER info into the extra log info
 */
class ServerInfoProcessor
{

    /**
     * The $_SERVER[...] keys to include in the extra data
     *
     * @var string[]
     */
    protected $includeKeys = [];

    /**
     * ServerInfoProcessor constructor.
     *
     * @param string[] $includeKeys The keys to pull from `$_SERVER`
     */
    public function __construct(array $includeKeys)
    {
        $this->includeKeys = $includeKeys;
    }

    /**
     * Invoke this processor
     *
     * @param array $record The given monolog record
     *
     * @return array The modified record
     */
    public function __invoke(array $record)
    {
        if (!isset($record['extra']['_SERVER'])) {
            $record['extra']['_SERVER'] = [];
        }

        foreach ($this->includeKeys as $includeKey) {
            if (isset($_SERVER[$includeKey])) {
                $record['extra']['_SERVER'][$includeKey] = $_SERVER[$includeKey];
            }
        }

        return $record;
    }

}


