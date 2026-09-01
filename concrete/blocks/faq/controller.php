<?php
namespace Concrete\Block\Faq;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\RichTextExtractor;

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, FileTrackableInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * @var string|null
     */
    public $blockTitle;

    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 465;
    protected $btTable = 'btFaq';
    protected $btExportTables = ['btFaq', 'btFaqEntries'];
    protected $btExportContentColumns = ['description'];
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputOnEditMode = true;

    public function getBlockTypeName()
    {
        return t('FAQ');
    }

    public function getBlockTypeDescription()
    {
        return t('Frequently Asked Questions Block');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::FAQ,
        ];
    }

    public function getSearchableContent()
    {
        $content = '';
        $db = $this->app->make('database')->connection();
        $v = [$this->bID];
        $q = 'SELECT * FROM btFaqEntries WHERE bID = ?';
        $r = $db->executeQuery($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['linkTitle'] . ' ' . $row['description'];
        }

        return $content;
    }

    public function add()
    {
        $this->set('rows', []);
    }

    public function edit()
    {
        $db = $this->app->make('database')->connection();
        $rows = $db->fetchAll('SELECT * FROM btFaqEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);

        $query = [];
        foreach ($rows as $q) {
            $q['description'] = LinkAbstractor::translateFromEditMode($q['description']);
            $query[] = $q;
        }

        $this->set('rows', $query);
    }

    public function view()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->fetchAll('SELECT * FROM btFaqEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);

        $rows = [];
        foreach ($query as $row) {
            $row['description'] = LinkAbstractor::translateFrom($row['description']);
            $rows[] = $row;
        }

        $this->set('rows', $rows);
    }

    public function duplicate($newBID)
    {
        $db = $this->app->make(Connection::class);
        $copyFields = 'title, linkTitle, description, sortOrder';
        $db->executeUpdate(
            "INSERT INTO btFaqEntries (bID, {$copyFields}) SELECT ?, {$copyFields} FROM btFaqEntries WHERE bID = ?",
            [
                $newBID,
                $this->bID,
            ]
        );
    }

    public function delete()
    {
        $db = $this->app->make('database')->connection();
        $db->executeQuery('DELETE FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        parent::delete();
    }

    public function save($args)
    {
        $db = $this->app->make('database')->connection();
        $db->executeQuery('DELETE FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        parent::save($args);
        $count = isset($args['sortOrder']) ? count($args['sortOrder']) : 0;

        $i = 0;
        while ($i < $count) {
            if (isset($args['description'][$i])) {
                $args['description'][$i] = LinkAbstractor::translateTo($args['description'][$i]);
            }

            $db->executeQuery(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) VALUES(?,?,?,?,?)',
                [
                    $this->bID,
                    $args['title'][$i],
                    $args['linkTitle'][$i],
                    $args['description'][$i],
                    $args['sortOrder'][$i],
                ]
            );
            ++$i;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        $nodesToRemove = $blockNode->xpath('./data[@table="btFaqEntries"]/record/id');
        if ($nodesToRemove) {
            foreach ($nodesToRemove as $nodeToRemove) {
                unset($nodeToRemove[0]);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        $schemaFactory = $this->app->make(ApiValueSchemaFactory::class);

        return [
            'type' => 'object',
            'properties' => [
                'blockTitle' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                ],
                'entries' => [
                    'type' => 'array',
                    'description' => 'The questions and their answers, in the order they are displayed. When writing, if this key is omitted the current ones are kept as they are.',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'linkTitle' => ['type' => 'string'],
                            'title' => ['type' => 'string'],
                            'description' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_CONTENT),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        $records = $this->serializeTablesForApi();
        $entries = [];
        foreach ($records['btFaqEntries'] ?? [] as $entry) {
            // the entries are listed in their display order, so their sort order is implicit
            unset($entry['sortOrder']);
            $entries[] = $entry;
        }

        return array_merge($records['btFaq'][0] ?? [], ['entries' => $entries]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        $entries = $this->deserializeRecordsFromApi($value, 'entries');
        $args = parent::getImportDataFromApiValue($page, $value);
        // the save() method wants the values of the entries in parallel arrays
        $args['linkTitle'] = [];
        $args['title'] = [];
        $args['description'] = [];
        $args['sortOrder'] = [];
        foreach (array_values($entries) as $index => $entry) {
            $args['linkTitle'][] = $entry['linkTitle'] ?? '';
            $args['title'][] = $entry['title'] ?? '';
            $args['description'][] = $entry['description'] ?? '';
            $args['sortOrder'][] = $index;
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        $result = [];
        $extractor = $this->app->make(RichTextExtractor::class);
        $db = $this->app->make(Connection::class);
        $descriptions = $db->fetchFirstColumn('SELECT description FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        foreach ($descriptions as $description) {
            $result = array_merge($result, $extractor->extractFiles($description));
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::importAdditionalData()
     */
    protected function importAdditionalData($b, $blockNode)
    {
        $nodesToRemove = $blockNode->xpath('./data[@table="btFaqEntries"]/record/id');
        if ($nodesToRemove) {
            foreach ($nodesToRemove as $nodeToRemove) {
                unset($nodeToRemove[0]);
            }
        }
        parent::importAdditionalData($b, $blockNode);
    }
}
