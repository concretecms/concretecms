<?php
namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollection;
use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Entity\Board\SlotTemplate;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class CollectionFactory implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        Channels::CHANNEL_CONTENT;
    }

    /**
     * @var AvailableTemplateCollectionFactory
     */
    protected $availableTemplateCollectionFactory;

    public function __construct(AvailableTemplateCollectionFactory $availableTemplateCollectionFactory)
    {
        $this->availableTemplateCollectionFactory = $availableTemplateCollectionFactory;
    }

    /**
     * @param SlotTemplate[] $availableTemplates
     * @param Instance $instance
     * @param int $slot
     * @return SlotTemplate
     */
    protected function getTemplateForSlot($filteredTemplates, int $totalItemsRemaining)
    {
        shuffle($filteredTemplates);
        foreach($filteredTemplates as $filteredTemplate) {
            if ($filteredTemplate->getDriver()->getTotalContentSlots() <= $totalItemsRemaining) {
                return $filteredTemplate;
            }
        }
    }

    /**
     * @param Instance $instance
     * @param ObjectInterface[] $items
     * @return ArrayCollection
     */
    public function createSlotCollection(Instance $instance, array $contentObjectGroups) : ArrayCollection
    {
        $collection = new ArrayCollection();
        $totalItemsRemaining = count($contentObjectGroups);
        $currentSlot = 1;
        while($totalItemsRemaining > 0) {
            $availableTemplates = $this->availableTemplateCollectionFactory->getAvailableTemplates($instance, $currentSlot);
            $template = $this->getTemplateForSlot($availableTemplates, $totalItemsRemaining);
            if ($template) {
                $slot = new InstanceSlot();
                $slot->setSlot($currentSlot);
                $slot->setInstance($instance);
                $slot->setTemplate($template);

                $collection->add($slot);

                $templateContentSlots = $template->getDriver()->getTotalContentSlots();
                $totalItemsRemaining -= $templateContentSlots;

                $this->logger->debug(t('Instance slot added to slot %s with template %s - items remaining %s',
                    $currentSlot, $template->getName(), $totalItemsRemaining
                ));

            }
            $currentSlot++;
        }

        return $collection;
    }

}
