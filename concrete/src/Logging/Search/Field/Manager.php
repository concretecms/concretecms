<?php

namespace Concrete\Core\Logging\Search\Field;

use Concrete\Core\Logging\Search\Field\Field\ChannelField;
use Concrete\Core\Logging\Search\Field\Field\DateField;
use Concrete\Core\Logging\Search\Field\Field\KeywordsField;
use Concrete\Core\Logging\Search\Field\Field\LevelField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{

    public function __construct()
    {
        $properties = [
            new KeywordsField(),
            new ChannelField(),
            new LevelField(),
            new DateField()
        ];

        $this->addGroup(t('Core Properties'), $properties);
    }

}
