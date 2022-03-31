<?php
namespace Concrete\Core\Entity\Board;

use Concrete\Core\Board\Instance\Slot\Template\AvailableTemplateCollectionFactory;
use Concrete\Core\Board\Permissions\PermissionsManager;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Permission\Assignment\BoardAssignment;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Permission\Response\BoardResponse;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Export\Item\Board as BoardExporter;
/**
 * @ORM\Entity
 * @ORM\Table(name="Boards")
 */
class Board implements ObjectInterface, AssignableObjectInterface, \JsonSerializable, ExportableInterface
{

    use AssignableObjectTrait;

    const ORDER_BY_RELEVANT_DATE_DESC = 'relevant_date_desc';
    const ORDER_BY_RELEVANT_DATE_ASC = 'relevant_date_asc';

    use PackageTrait;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $boardID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $boardName;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Site\Site")
     * @ORM\JoinColumn(name="siteID", referencedColumnName="siteID")
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource", cascade={"remove"}, mappedBy="board")
     */
    protected $data_sources;

    /**
     * @ORM\OneToMany(targetEntity="Instance", cascade={"remove"}, mappedBy="board", fetch="EXTRA_LAZY")
     */
    protected $instances;

    /**
     * @ORM\OneToMany(targetEntity="BoardPermissionAssignment", mappedBy="board", cascade={"remove"})
     */
    protected $permission_assignments;

    /**
     * @ORM\ManyToOne(targetEntity="Template")
     */
    protected $template;

    /**
     * Values include relevant_date_asc, relevant_date_desc
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $sortBy = self::ORDER_BY_RELEVANT_DATE_ASC;

    /**
     * @ORM\ManyToMany(targetEntity="Concrete\Core\Entity\Board\SlotTemplate")
     * @ORM\JoinTable(name="BoardCustomSlotTemplates",
     *      joinColumns={@ORM\JoinColumn(name="boardID", referencedColumnName="boardID")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="slotTemplateID", referencedColumnName="id")}
     *      )
     */
    protected $custom_slot_templates;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hasCustomSlotTemplates = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hasCustomWeightingRules = false;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    protected $overridePermissions = false;

    public function __construct()
    {
        $this->data_sources = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->batches = new ArrayCollection();
        $this->instances = new ArrayCollection();
        $this->custom_slot_templates = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getBoardID()
    {
        return $this->boardID;
    }

    /**
     * @return mixed
     */
    public function getBoardName()
    {
        return $this->boardName;
    }

    /**
     * @param mixed $boardName
     */
    public function setBoardName($boardName): void
    {
        $this->boardName = $boardName;
    }

    /**
     * @return ConfiguredDataSource[]
     */
    public function getDataSources()
    {
        return $this->data_sources;
    }

    /**
     * @param mixed $data_sources
     */
    public function setDataSources($data_sources): void
    {
        $this->data_sources = $data_sources;
    }

    /**
     * @return mixed
     */
    public function getTemplate() : Template
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template): void
    {
        $this->template = $template;
    }

    /**
     * @return mixed
     */
    public function getCustomSlotTemplates()
    {
        return $this->custom_slot_templates;
    }

    /**
     * @param mixed $custom_slot_templates
     */
    public function setCustomSlotTemplates($custom_slot_templates)
    {
        $this->custom_slot_templates = $custom_slot_templates;
    }

    /**
     * @return mixed
     */
    public function hasCustomSlotTemplates()
    {
        return $this->hasCustomSlotTemplates;
    }

    /**
     * @param mixed $hasCustomSlotTemplates
     */
    public function setHasCustomSlotTemplates($hasCustomSlotTemplates)
    {
        $this->hasCustomSlotTemplates = $hasCustomSlotTemplates;
    }

    /**
     * @return mixed
     */
    public function hasCustomWeightingRules()
    {
        return $this->hasCustomWeightingRules;
    }

    /**
     * @param mixed $hasCustomWeightingRules
     */
    public function setHasCustomWeightingRules($hasCustomWeightingRules): void
    {
        $this->hasCustomWeightingRules = $hasCustomWeightingRules;
    }

    /**
     * @return mixed
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * @param mixed $instances
     */
    public function setInstances($instances): void
    {
        $this->instances = $instances;
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return mixed
     */
    public function arePermissionsSetToOverride()
    {
        return $this->overridePermissions;
    }

    /**
     * @param mixed $overridePermissions
     */
    public function setOverridePermissions($overridePermissions)
    {
        $this->overridePermissions = $overridePermissions;
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getBoardID();
    }

    public function getPermissionAssignmentClassName()
    {
        return BoardAssignment::class;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'board';
    }

    public function getPermissionResponseClassName()
    {
        return BoardResponse::class;
    }

    public function setChildPermissionsToOverride()
    {
        return false;
    }

    public function __toString()
    {
        return (string) $this->getBoardID();
    }

    public function setPermissionsToOverride()
    {
        $app = Facade::getFacadeApplication();
        $manager = $app->make(PermissionsManager::class);
        /**
         * @var $manager PermissionsManager
         */
        $manager->setPermissionsToOverride($this);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getBoardID(),
            'name' => $this->getBoardName(),
            'template' => $this->getTemplate(),
            'slotTemplates' => app(AvailableTemplateCollectionFactory::class)->getBoardSlotTemplates($this),
        ];
    }

    public function getExporter()
    {
        return app(BoardExporter::class);
    }


}
