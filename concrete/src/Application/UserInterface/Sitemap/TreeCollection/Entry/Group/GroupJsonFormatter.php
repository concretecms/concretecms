<?php
namespace Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\Entry\Group;

final class GroupJsonFormatter implements \JsonSerializable
{

    protected $entryGroup;

    public function __construct(GroupInterface $entryGroup)
    {
        $this->entryGroup = $entryGroup;
    }

    public function jsonSerialize()
    {
        $response = array(
            'value' => $this->entryGroup->getEntryGroupIdentifier(),
            'label' => $this->entryGroup->getEntryGroupLabel()
        );
        return $response;
    }


}
