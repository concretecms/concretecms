<?php

namespace Concrete\Core\Health\Report\Result\Formatter;

use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SearchResult;

class SearchResultFormatter implements FormatterInterface
{

    /**
     * @param SearchResult $result
     * @return string
     */
    public function getFindingsHeading(Result $result): string
    {
        if ($result->getSearchType() === $result::TYPE_TAG) {
            return t('<b>&lt;%s&gt;</b> tag found in the following locations:', $result->getSearchString());
        } elseif ($result->getSearchType() === $result::TYPE_KEYWORDS) {
            return t('"<b>%s</b>" string found in the following locations:', $result->getSearchString());
        }

    }


}