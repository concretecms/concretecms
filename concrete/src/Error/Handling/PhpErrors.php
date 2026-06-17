<?php

namespace Concrete\Core\Error\Handling;

class PhpErrors implements \JsonSerializable
{

    const TYPE_ERROR = 'error'; // maps to E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR,
    const TYPE_WARNING = 'warning'; // maps to E_WARNING, E_CORE_WARNING, E_USER_WARNING,
    const TYPE_NOTICE = 'notice'; // maps to E_NOTICE, E_USER_NOTICE,
    const TYPE_DEPRECATED = 'deprecated'; // maps to E_DEPRECATED, E_USER_DEPRECATED

    public function getErrorTypes(): array
    {
        return [
            self::TYPE_ERROR => t('Fatal'),
            self::TYPE_WARNING => t('Warning'),
            self::TYPE_NOTICE => t('Notice'),
            self::TYPE_DEPRECATED => t('Deprecated Code')
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [];
        foreach ($this->getErrorTypes() as $type => $title) {
            $data[] = [
                'type' => $type,
                'title' => $title,
            ];
        }
        return $data;
    }
}
