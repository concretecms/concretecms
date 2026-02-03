<?php

namespace Concrete\Block\Accordion;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\RichTextExtractor;
use InvalidArgumentException;

class AccordionEntry implements \JsonSerializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * AccordionEntry constructor.
     *
     * @param int $id
     * @param string $title
     * @param string $description
     */
    public function __construct($id, $title, $description)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'expanded' => false,
        ];
    }
}

class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    /**
     * @var string|null
     */
    public $initialState;

    /**
     * @var string|null
     */
    public $itemHeadingFormat;

    /**
     * @var int|string|null
     */
    public $alwaysOpen;

    /**
     * @var int|string|null
     */
    public $flush;

    /**
     * @var int
     */
    protected $btInterfaceWidth = 720;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 580;

    /**
     * @var string
     */
    protected $btTable = 'btAccordion';

    /**
     * @var string[]
     */
    protected $helpers = ['form'];

    /**
     * @var string[]
     */
    protected $btExportTables = ['btAccordion', 'btAccordionEntries'];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportContentColumns
     */
    protected $btExportContentColumns = ['description'];

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * @var string
     */
    protected $btDefaultSet = 'navigation';

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = true;

    public function getBlockTypeName()
    {
        return t('Accordion');
    }

    public function getBlockTypeDescription()
    {
        return t('Collapsible content block.');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::ACCORDIONS,
        ];
    }

    /**
     * @return string
     * @throws \Doctrine\DBAL\Exception
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getSearchableContent()
    {
        $content = '';
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $v = [$this->bID];
        $q = 'SELECT * FROM btAccordionEntries WHERE bID = ? order by sortOrder';
        $r = $db->executeQuery($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['description'];
        }

        return $content;
    }

    /**
     * @return void
     * @throws \Doctrine\DBAL\Exception
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function edit()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $query = $db->fetchAllAssociative(
            'SELECT * FROM btAccordionEntries WHERE bID = ? ORDER BY sortOrder',
            [$this->bID]
        );

        $entries = [];
        foreach ($query as $row) {
            $entry = new AccordionEntry(
                $row['id'],
                $row['title'],
                LinkAbstractor::translateFromEditMode($row['description'])
            );
            $entries[] = $entry;
        }

        $this->set('entries', $entries);
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function view()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $query = $db->fetchAllAssociative(
            'SELECT * FROM btAccordionEntries WHERE bID = ? ORDER BY sortOrder',
            [$this->bID]
        );

        $entries = [];
        foreach ($query as $row) {
            $entry = new AccordionEntry($row['id'], $row['title'], LinkAbstractor::translateFrom($row['description']));
            $entries[] = $entry;
        }

        $this->set('entries', $entries);
    }

    /**
     * @param int $newBID
     *
     * @return null
     * @throws \Doctrine\DBAL\Exception
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function duplicate($newBID)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $copyFields = 'title, description, sortOrder';
        $db->executeStatement(
            "INSERT INTO btAccordionEntries (bID, {$copyFields}) SELECT ?, {$copyFields} FROM btAccordionEntries WHERE bID = ?",
            [
                $newBID,
                $this->bID,
            ]
        );

        return null;
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->set('entries', []);
        $this->set('itemHeadingFormat', 'h2');
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $db->executeStatement('DELETE FROM btAccordionEntries WHERE bID = ?', [$this->bID]);
        parent::delete();
    }

    /**
     * @param mixed[] $args
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function save($args)
    {
        parent::save($args);

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $db->executeStatement('DELETE FROM btAccordionEntries WHERE bID = ?', [$this->bID]);
        $entries = $this->processJson($args);

        if ($entries) {
            $sortOrder = 0;
            foreach ($entries as $entry) {
                // Add the entry row
                if (isset($entry['description'])) {
                    $entry['description'] = LinkAbstractor::translateTo($entry['description']);
                }
                $db->executeStatement(
                    'INSERT INTO btAccordionEntries (bID, sortOrder, title, description) VALUES (?, ?, ?, ?)',
                    [(int)$this->bID, $sortOrder++, $entry['title'], $entry['description']]
                );
            }
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
        $nodesToRemove = $blockNode->xpath('./data[@table="btAccordionEntries"]/record/id');
        if ($nodesToRemove) {
            foreach ($nodesToRemove as $nodeToRemove) {
                unset($nodeToRemove[0]);
            }
        }
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
        $descriptions = $db->fetchFirstColumn('SELECT description FROM btAccordionEntries WHERE bID = ?', [$this->bID]);
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
        $nodesToRemove = $blockNode->xpath('./data[@table="btAccordionEntries"]/record/id');
        if ($nodesToRemove) {
            foreach ($nodesToRemove as $nodeToRemove) {
                unset($nodeToRemove[0]);
            }
        }
        parent::importAdditionalData($b, $blockNode);
    }

    /**
     * Process an inputted json into a proper json object.
     *
     * @param array<string,mixed> $args The equivalent to the $_POST submitted
     *
     * @return array<string,mixed>
     * @throws InvalidArgumentException If the $args or the json are invalid
     *
     */
    protected function processJson(array $args): array
    {
        $json = trim($args['accordionBlockData'] ?? '[]');

        if (!$json || $json[0] !== '[') {
            throw new InvalidArgumentException('Invalid request.');
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid request.');
        }

        return $data;
    }
}
