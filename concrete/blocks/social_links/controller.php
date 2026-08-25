<?php
namespace Concrete\Block\SocialLinks;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Sharing\SocialNetwork\Link;
use Database;
use Core;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * @var int|string|null
     */
    public $btSocialLinkID;

    /**
     * @var int|string|null
     */
    public $slID;

    /**
     * @var int|string|null
     */
    public $displayOrder;

    public $helpers = ['form'];

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputOnEditMode = true;
    protected $btInterfaceHeight = 400;
    protected $btTable = 'btSocialLinks';

    public function getBlockTypeDescription()
    {
        return t("Allows users to add social icons to their website");
    }

    public function getBlockTypeName()
    {
        return t("Social Links");
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::SOCIAL
        ];
    }

    public function edit()
    {
        $all = Link::getList();

        // first we populate the links list with the selected ones in the proper order.
        $final = $selected = $this->getSelectedLinks();
        foreach ($all as $link) {
            if (!in_array($link, $selected)) {
                $final[] = $link;
            }
        }
        $this->set('links', $final);
        $this->set('selectedLinks', $selected);
    }

    public function add()
    {
        $links = Link::getList();
        $this->set('links', $links);
        $this->set('selectedLinks', []);
    }

    protected function getSelectedLinks()
    {
        $links = [];
        $db = Database::get();
        $slIDs = (array) $db->GetCol('select slID from btSocialLinks where bID = ? order by displayOrder asc',
            [$this->bID]
        );
        foreach ($slIDs as $slID) {
            $link = Link::getByID($slID);
            if (is_object($link)) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'links' => [
                    'type' => 'array',
                    'description' => 'The social links of the site to be displayed, in the order they are displayed (the ones that the site doesn\'t have are left out).',
                    'items' => [
                        'type' => 'string',
                        'description' => 'The handle of the service of one of the social links of the site (for instance "bluesky").',
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        if (!array_key_exists('links', $value)) {
            $value = $this->bID ? $this->serializeValueForApi() : ['links' => []];
        }
        // the block stores the ID of every link, which is meaningful only within this site
        $args = ['slID' => []];
        foreach ((array) $value['links'] as $serviceHandle) {
            $link = is_string($serviceHandle) ? Link::getByServiceHandle($serviceHandle) : null;
            if ($link !== null) {
                $args['slID'][] = $link->getID();
            }
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        // the export() method below writes an element for every link, which is not the list of records of a
        // database table that the API knows how to read
        $links = [];
        foreach ($this->getSelectedLinks() as $link) {
            $links[] = (string) $link->getServiceObject()->getHandle();
        }

        return ['links' => $links];
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        foreach ($this->getSelectedLinks() as $link) {
            $linkNode = $blockNode->addChild('link');
            $linkNode->addAttribute('service', $link->getServiceObject()->getHandle());
        }
    }

    public function getImportData($blockNode, $page)
    {
        $args = [];
        foreach ($blockNode->link as $link) {
            $link = Link::getByServiceHandle((string) $link['service']);
            if ($link) {
                $args['slID'][] = $link->getID();
            }
        }

        return $args;
    }

    public function validate($args)
    {
        $e = Core::make('helper/validation/error');
        if (!isset($args['slID']) || empty($args['slID'])) {
            $e->add(t('You must choose at least one link.'));
        }

        return $e;
    }

    public function duplicate($newBlockID)
    {
        $db = Database::get();
        $displayOrder = 0;
        foreach ($this->getSelectedLinks() as $link) {
            $db->insert('btSocialLinks', ['bID' => $newBlockID, 'slID' => $link->getID(), 'displayOrder' => $displayOrder]);
            $displayOrder++;
        }
    }

    public function save($args)
    {
        $db = Database::get();
        $db->delete('btSocialLinks', ['bID' => $this->bID]);
        $slIDs = empty($args['slID']) ? [] : (array) $args['slID'];

        $statement = $db->prepare('insert into btSocialLinks (bID, slID, displayOrder) values (?, ?, ?)');
        $displayOrder = 0;
        foreach ($slIDs as $linkID) {
            $statement->bindValue(1, $this->bID);
            $statement->bindValue(2, $linkID);
            $statement->bindValue(3, $displayOrder);
            $statement->execute();
            ++$displayOrder;
        }
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btSocialLinks', ['bID' => $this->bID]);
    }

    public function view()
    {
        $links = $this->getSelectedLinks();
        $this->set('links', $links);
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'font-awesome');
    }
}
