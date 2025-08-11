<?php
namespace Concrete\Core\Summary\Data\Field;

use League\Url\Url;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Represents a link to a page on your site by path. Useful to separate these from the basic link because these
 * paths are run through the URL::to() mechanism to ensure they work irrespective of pretty URL settings,
 * and when Concrete is installed in a subdirectory.
 */
class PagePathLinkDataFieldData extends LinkDataFieldData
{

    public function __toString()
    {
        return (string) app('url/resolver/path')->resolve([(string) $this->link]);
    }


}
