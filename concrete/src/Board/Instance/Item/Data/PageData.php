<?php
namespace Concrete\Core\Board\Instance\Item\Data;

use Concrete\Core\Page\Page;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class PageData implements DataInterface
{

    /**
     * @var int
     */
    protected $cID = 0;

    public function __construct(Page $c = null)
    {
        if ($c) {
            $this->cID = $c->getCollectionID();
        }
    }

    /**
     * @return int
     */
    public function getPageID(): int
    {
        return $this->cID;
    }

    /**
     * @param int $cID
     */
    public function setPageID(int $cID): void
    {
        $this->cID = $cID;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['cID' => $this->cID];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        $this->cID = $data['cID'];
    }

}
