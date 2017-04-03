<?php
namespace Concrete\Core\Localization\Locale;

interface LocaleCollectionAggregateInterface
{
    /**
     * @return LocaleCollection
     */
    public function getLocaleCollection();
}
