<?php
namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
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
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param SlotTemplate[] $availableTemplates
     * @param Instance $instance
     * @param int $slot
     * @return SlotTemplate
     */
    protected function getTemplateForSlot($availableTemplates, Instance $instance, int $slot, int $totalItemsRemaining)
    {
        $availableTemplatesByFormFactor = [];
        foreach($availableTemplates as $availableTemplate) {
            $availableTemplatesByFormFactor[$availableTemplate->getFormFactor()][] = $availableTemplate;
        }

        $driver = $instance->getBoard()->getTemplate()->getDriver();
        $formFactor = $driver->getFormFactor();
        if (is_array($formFactor)) {
            $formFactor = $formFactor[$slot];
        } else {
            $formFactor = $driver->getFormFactor();
        }

        $filteredTemplates = $availableTemplatesByFormFactor[$formFactor];
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

        $board = $instance->getBoard();
        if ($board->hasCustomSlotTemplates()) {
            $availableTemplates = $board->getCustomSlotTemplates();
        } else {
            $availableTemplates = $this->entityManager->getRepository(SlotTemplate::class)->findAll();
        }

        $collection = new ArrayCollection();
        $totalItemsRemaining = count($contentObjectGroups);

        $currentSlot = 1;
        while($totalItemsRemaining > 0) {
            $template = $this->getTemplateForSlot($availableTemplates, $instance, $currentSlot, $totalItemsRemaining);
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
