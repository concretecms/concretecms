<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Attribute\CategoryObjectInterface;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Express\Controller\ControllerInterface;
use Concrete\Core\Express\Search\ColumnSet\ColumnSet;
use Concrete\Core\Express\Search\ColumnSet\DefaultSet;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\ExpressEntryResults;
use Concrete\Core\Export\Item\Express\Entity as EntityExporter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use League\Url\UrlInterface;
use Concrete\Block\ExpressForm\Controller as ExpressFormBlockController;

/**
 * @ORM\Entity(repositoryClass="\Concrete\Core\Entity\Express\EntityRepository")
 * @ORM\Table(name="ExpressEntities")
 * @ORM\EntityListeners({"\Concrete\Core\Express\Entity\Listener"})
 */
class Entity implements CategoryObjectInterface, ObjectInterface, ExportableInterface
{

    const DEFAULT_ITEMS_PER_PAGE = 10;

    use PackageTrait;

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $handle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $plural_handle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label_mask;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $supports_custom_display_order = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    protected $result_column_set;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $include_in_public_list = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_published = true;

    /**
     * @ORM\Column(type="integer")
     */
    protected $entity_results_node_id;

    /**
     * If true, this entity splits its results by multisite. If false, it is shared across all sites. Default should
     * probably be true, but for backwards compatibility it is false.
     *
     * @ORM\Column(type="boolean")
     */
    protected $use_separate_site_result_buckets = false;

    /**
     * @ORM\Column(type="integer")
     */
    protected $items_per_page = 10;


    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Key\ExpressKey", mappedBy="entity", cascade={"persist", "remove"})
     **/
    protected $attributes;

    /**
     * @ORM\OneToMany(targetEntity="Association", mappedBy="source_entity", cascade={"persist", "remove"})
     **/
    protected $associations;

    /**
     * @ORM\OneToMany(targetEntity="Form", mappedBy="entity", cascade={"persist", "remove"})
     **/
    protected $forms;

    /**
     * @ORM\OneToOne(targetEntity="Form", cascade={"persist"})
     **/
    protected $default_view_form;

    /**
     * @ORM\OneToOne(targetEntity="Form", cascade={"persist"})
     **/
    protected $default_edit_form;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="entity", cascade={"persist", "remove"})
     **/
    protected $entries;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_date;

    public function __construct()
    {
        $this->created_date = new \DateTime();
        $this->attributes = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param mixed $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getPluralHandle()
    {
        return $this->plural_handle;
    }

    /**
     * @param mixed $plural_handle
     */
    public function setPluralHandle($plural_handle)
    {
        $this->plural_handle = $plural_handle;
    }

    /**
     * @return mixed
     */
    public function supportsCustomDisplayOrder()
    {
        return $this->supports_custom_display_order;
    }

    /**
     * @param mixed $supports_custom_display_order
     */
    public function setSupportsCustomDisplayOrder($supports_custom_display_order)
    {
        $this->supports_custom_display_order = $supports_custom_display_order;
    }

    /**
     * If an entity supports entry-specific permissions, we will check its permissions during any listing
     * events, and use simple Pager next/back cursor pagination (unless the logged-in user is the super user).
     * This method determines whether this entity supports entry specific permissions. Currently, this is only
     * true if the entity has separate site-specific buckets, but in the future we might make this an option
     * that admins can set on the entity themselves.
     *
     * @return bool
     */
    public function supportsEntrySpecificPermissions(): bool
    {
        if ($this->usesSeparateSiteResultsBuckets()) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getOwnedBy()
    {
        foreach($this->associations as $association) {
            if ($association->isOwnedByAssociation()) {
                return $association->getTargetEntity();
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIncludeInPublicList()
    {
        return $this->include_in_public_list;
    }

    /**
     * @param mixed $include_in_public_list
     */
    public function setIncludeInPublicList($include_in_public_list)
    {
        $this->include_in_public_list = $include_in_public_list;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * @param bool $is_published
     */
    public function setIsPublished(bool $is_published): void
    {
        $this->is_published = $is_published;
    }

    /**
     * @return string
     */
    public function getLabelMask()
    {
        return $this->label_mask ?: '';
    }

    /**
     * @param string $label_mask
     */
    public function setLabelMask($label_mask)
    {
        $this->label_mask = trim($label_mask);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $format
     * @return string
     */
    public function getEntityDisplayDescription($format = 'html')
    {
        $value = $this->getDescription();
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
    }

    /**
     * @return ColumnSet
     */
    public function getResultColumnSet()
    {
        $set = $this->result_column_set;
        if (is_object($set)) {
            return $set;
        } else {
            return new DefaultSet($this->getAttributeKeyCategory());
        }
    }

    /**
     * @param mixed $result_column_set
     */
    public function setResultColumnSet($result_column_set)
    {
        $this->result_column_set = $result_column_set;
    }

    /**
     * @return Form|null
     */
    public function getDefaultViewForm()
    {
        return $this->default_view_form;
    }

    /**
     * @param Form|null $default_view_form
     */
    public function setDefaultViewForm($default_view_form)
    {
        $this->default_view_form = $default_view_form;
    }

    /**
     * @return Form|null
     */
    public function getDefaultEditForm()
    {
        return $this->default_edit_form;
    }

    /**
     * @param Form|null $default_edit_form
     */
    public function setDefaultEditForm($default_edit_form)
    {
        $this->default_edit_form = $default_edit_form;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        if ($this->items_per_page) {
            return $this->items_per_page;
        } else {
            return self::DEFAULT_ITEMS_PER_PAGE;
        }
    }

    /**
     * @param mixed $items_per_page
     */
    public function setItemsPerPage($items_per_page)
    {
        $this->items_per_page = $items_per_page;
    }

    /**
     * @param string $format
     * @return string
     */
    public function getEntityDisplayName($format = 'html')
    {
        $value = $this->getName();
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * @return Collection<int, ExpressKey>|ExpressKey[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return Collection<int, Association>|Association[]
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param mixed $associations
     */
    public function setAssociations($associations)
    {
        $this->associations = $associations;
    }

    /**
     * @return Collection<int, Form>|Form[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @param mixed $forms
     */
    public function setForms($forms)
    {
        $this->forms = $forms;
    }

    public function getForm($name)
    {
        foreach($this->getForms() as $form) {
            if ($form->getName() == $name) {
                return $form;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getEntityResultsNodeId()
    {
        return $this->entity_results_node_id;
    }

    public function getEntityResultsNodeObject(Site $site = null)
    {
        $node = Node::getByID($this->getEntityResultsNodeId());
        if ($node) {
            /**
             * @var $node ExpressEntryResults
             */
            if ($site && $this->usesSeparateSiteResultsBuckets()) {
                $siteNode = $node->getSiteResultsNode($site);
                if ($siteNode) {
                    return $siteNode;
                }
            }
        }
        return $node;
    }

    /**
     * @param mixed $entity_results_node_id
     */
    public function setEntityResultsNodeId($entity_results_node_id)
    {
        $this->entity_results_node_id = $entity_results_node_id;
    }

    public function getAttributeKeyCategory()
    {
        return \Core::make('\Concrete\Core\Attribute\Category\ExpressCategory', array('entity' => $this));
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getId();
    }

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\ExpressEntityResponse';
    }

    public function getAssociation($handle)
    {
        foreach($this->associations as $association) {
            if ($association->getTargetPropertyName() == $handle) {
                return $association;
            }
        }
    }

    /**
     * @return mixed
     */
    public function usesSeparateSiteResultsBuckets()
    {
        return $this->use_separate_site_result_buckets;
    }

    /**
     * @param mixed $use_separate_site_result_buckets
     */
    public function setUseSeparateSiteResultBuckets($use_separate_site_result_buckets)
    {
        $this->use_separate_site_result_buckets = $use_separate_site_result_buckets;
    }



    public function getPermissionAssignmentClassName()
    {
        return false;
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return false;
    }

    public function getExporter()
    {
        return new EntityExporter();
    }

    /**
     * @return ControllerInterface
     */
    public function getController()
    {
        return app('express')->getEntityController($this);
    }

    /**
     * @TODO - add a new interface for managing this within an express entity's controller, perhaps in 9.2.0.
     * It should have the same functionality in the StandardController, but this will allow custom controllers
     * to change how this functions
     *
     * @return UrlInterface
     */
    public function getEntryListingUrl(): UrlInterface
    {
        $node = $this->getEntityResultsNodeObject();
        $parent = $node->getTreeNodeParentObject();
        $url = app('url/resolver/path');
        if ($parent && $parent->getTreeNodeTypeHandle() == 'express_entry_category' && $parent->getTreeNodeName() ==
            ExpressFormBlockController::FORM_RESULTS_CATEGORY_NAME) {
            return $url->resolve(['/dashboard/reports/forms/results', $this->getId()]);
        } else {
            return $url->resolve(['/dashboard/express/entries/results', $this->getId()]);
        }
    }

    public function __clone()
    {
        $this->id = null;
        $this->attributes = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->entries = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->default_edit_form = null;
        $this->default_view_form = null;
        $this->created_date = new \DateTime();
    }
}
