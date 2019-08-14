<?php
namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\Control\Type\SaveHandler\AssociationControlSaveHandler;
use Concrete\Core\Form\Context\Registry\ControlRegistry;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ExpressFormFieldSetAssociationControls")
 * @since 8.0.0
 */
class AssociationControl extends Control
{
    /**
     * @since 8.5.0
     */
    const TYPE_HTML_INPUT = 0;
    /**
     * @since 8.5.0
     */
    const TYPE_ENTRY_SELECTOR = 5;

    /**
     * @var \Concrete\Core\Entity\Express\Association
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Express\Association", inversedBy="controls")
     */
    protected $association;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $association_entity_label_mask;

    /**
     * The association selector mode (one of the self::TYPE_... constants).
     *
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     *
     * @var int
     * @since 8.5.0
     */
    protected $entry_selector_mode;

    /**
     * Does the control give us the ability to reorder entries in the association?
     *
     * @ORM\Column(type="boolean")
     * @since 8.5.0
     */
    protected $enable_entry_reordering = false;


    /**
     * @since 8.5.0
     */
    public function __construct()
    {
        $this->entry_selector_mode = self::TYPE_HTML_INPUT;
    }

    /**
     * @return mixed
     */
    public function getAssociationEntityLabelMask()
    {
        return $this->association_entity_label_mask;
    }

    /**
     * @since 8.2.0
     */
    public function getControlView(ContextInterface $context)
    {
        $registry = \Core::make(ControlRegistry::class);

        return $registry->getControlView($context, 'express_control_association', [
            $this,
        ]);
    }

    /**
     * @param mixed $association_entity_label_mask
     */
    public function setAssociationEntityLabelMask($association_entity_label_mask)
    {
        $this->association_entity_label_mask = $association_entity_label_mask;
    }

    /**
     * @return int
     * @since 8.5.0
     */
    public function getEntrySelectorMode()
    {
        return $this->entry_selector_mode;
    }

    /**
     * @param int $entry_selector_mode
     * @since 8.5.0
     */
    public function setEntrySelectorMode($entry_selector_mode)
    {
        $this->entry_selector_mode = $entry_selector_mode;
    }

    /**
     * @return Association
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * @param mixed $association
     */
    public function setAssociation($association)
    {
        $this->association = $association;
    }

    public function getControlSaveHandler()
    {
        return new AssociationControlSaveHandler();
    }

    public function getControlLabel()
    {
        return $this->getAssociation()->getTargetEntity()->getName();
    }

    public function getType()
    {
        return 'association';
    }

    public function getExporter()
    {
        return new \Concrete\Core\Export\Item\Express\Control\AssociationControl();
    }

    /**
     * @return mixed
     * @since 8.5.0
     */
    public function enableEntryReordering()
    {
        return $this->enable_entry_reordering;
    }

    /**
     * @param mixed $enable_entry_reordering
     * @since 8.5.0
     */
    public function setEnableEntryReordering($enable_entry_reordering)
    {
        $this->enable_entry_reordering = $enable_entry_reordering;
    }


}
