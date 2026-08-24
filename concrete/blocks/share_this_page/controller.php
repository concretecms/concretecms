<?php
namespace Concrete\Block\ShareThisPage;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Sharing\ShareThisPage\ServiceList;
use Concrete\Core\Sharing\ShareThisPage\Service;
use Concrete\Core\Utility\Service\Xml;
use SimpleXMLElement;
use Database;
use Core;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * @var int|string|null
     */
    public $btShareThisPageID;

    /**
     * @var string|null
     */
    public $service;

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
    protected $btTable = 'btShareThisPage';

    protected $services = [];

    public function getBlockTypeDescription()
    {
        return t("Allows users to share this page with social networks.");
    }

    public function getBlockTypeName()
    {
        return t("Share This Page");
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::SOCIAL
        ];
    }

    public function edit()
    {
        $this->set('selectedServices', $this->getSelectedServices());
        $this->set('availableServices', ServiceList::get());
    }

    public function add()
    {
        $this->edit();
    }

    public function addService($service)
    {
        $ss = Service::getByHandle($service);
        if (is_object($ss)) {
            $this->services[] = $ss;
        }
    }

    protected function getSelectedServices()
    {
        $this->services = [];
        $db = Database::get();
        $services = $db->GetCol('select service from btShareThisPage where bID = ? order by displayOrder asc',
            [$this->bID]
        );
        foreach ($services as $service) {
            $this->addService($service);
        }

        return $this->services;
    }

    public function duplicate($newBlockID)
    {
        $db = Database::get();
        $displayOrder = 0;
        foreach ($this->getSelectedServices() as $service) {
            $db->insert('btShareThisPage', ['bID' => $newBlockID, 'service' => $service->getHandle(), 'displayOrder' => $displayOrder]);
            $displayOrder++;
        }
    }

    public function validate($args)
    {
        $e = Core::make('helper/validation/error');
        if (!isset($args['service']) || empty($args['service'])) {
            $e->add(t('You must choose at least one service.'));
        }

        return $e;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        $handles = [];
        foreach (ServiceList::get() as $service) {
            $handles[] = (string) $service->getHandle();
        }

        return [
            'type' => 'object',
            'properties' => [
                'services' => [
                    'type' => 'array',
                    'description' => 'The services the visitors can share the page with, in the order they are displayed (the ones that don\'t exist are left out).',
                    'items' => [
                        'type' => 'string',
                        'enum' => $handles,
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
        if (!array_key_exists('services', $value)) {
            $value = $this->bID ? $this->serializeValueForApi() : ['services' => []];
        }
        // the getImportData() method below reads the handles out of the XML of a CIF file
        $blockNode = new SimpleXMLElement('<block></block>');
        $dataNode = $blockNode->addChild('data');
        $xml = $this->app->make(Xml::class);
        foreach ((array) $value['services'] as $service) {
            if (is_string($service)) {
                $xml->createChildElement($dataNode, 'service', $service);
            }
        }

        return $this->getImportData($blockNode, $page);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        // the export() method below writes the handles as the children of the <data> element, which is not
        // the list of records of a database table that the API knows how to read
        $blockNode = new SimpleXMLElement('<block></block>');
        $this->export($blockNode);
        $services = [];
        foreach ($blockNode->data->service as $service) {
            $services[] = (string) $service;
        }

        return ['services' => $services];
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $data = $blockNode->addChild('data');
        foreach ($this->getSelectedServices() as $link) {
            $data->addChild('service', $link->getHandle());
        }
    }

    public function getImportData($blockNode, $page)
    {
        $args = ['service' => []];
        foreach ($blockNode->data->service as $service) {
            $link = Service::getByHandle((string) $service);
            // the service may be provided by a package that this site doesn't have
            if (is_object($link)) {
                $args['service'][] = $link->getHandle();
            }
        }

        return $args;
    }

    public function save($args)
    {
        $db = Database::get();
        $db->delete('btShareThisPage', ['bID' => $this->bID]);
        $services = is_array($args['service'] ?? null) ? $args['service'] : [];

        $statement = $db->prepare('insert into btShareThisPage (bID, service, displayOrder) values (?, ?, ?)');
        $displayOrder = 0;
        foreach ($services as $service) {
            $statement->bindValue(1, $this->bID);
            $statement->bindValue(2, $service);
            $statement->bindValue(3, $displayOrder);
            $statement->execute();
            ++$displayOrder;
        }
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btShareThisPage', ['bID' => $this->bID]);
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'font-awesome');
    }

    public function view()
    {
        if (count($this->services) == 0) {
            $selected = $this->getSelectedServices();
        } else {
            $selected = $this->services;
        }
        $this->set('selected', $selected);
    }
}
