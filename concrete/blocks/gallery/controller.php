<?php

namespace Concrete\Block\Gallery;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File as ConcreteFile;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;
use Concrete\Core\Utility\Service\Number;
use Concrete\Core\Utility\Service\Xml;
use Generator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    protected $btTable = 'btGallery';
    protected $btInterfaceWidth = '750';
    protected $btInterfaceHeight = '820';
    protected $btExportTables = ['btGallery', 'btGalleryEntries', 'btGalleryEntryDisplayChoices'];
    protected $btExportFileColumns = ['fID'];
    protected $btCacheBlockRecord = false;
    /** @var int */
    protected $includeDownloadLink;
    /** @var Connection|null */
    private $connection;

    /** @var Number|null */
    private $numbers;

    /** @var ResolverManager|null */
    private $urls;

    /**
     * Return a list of features used by this block
     *
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::IMAGERY
        ];
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $blockNode->addChild('includeDownloadLink', $this->includeDownloadLink);

        // now we can do the entries.
        $entries = $this->getEntries();
        $entriesNode = $blockNode->addChild('entries');
        $xml = new Xml();
        foreach ($entries as $entry) {
            $entryNode = $entriesNode->addChild('entry');
            $entryNode->addChild('fID', ContentExporter::replaceFileWithPlaceHolder($entry['id']));
            $choicesNode = $entryNode->addChild('displaychoices');
            if (count($entry['displayChoices'])) {
                foreach ($entry['displayChoices'] as $dckey => $displayChoice) {
                    $choiceNode = $choicesNode->addChild('choice');
                    $choiceNode->addChild('dckey', $dckey);
                    $xml->createCDataNode($choiceNode, 'value', $displayChoice['value']);
                }
            }
        }
    }


    public function importAdditionalData($b, $blockNode)
    {
        $db = $this->app->make(Connection::class);
        $data['includeDownloadLink'] = (string)$blockNode->includeDownloadLink;
        if ($data['includeDownloadLink']) {
            $db->update('btGallery', $data, ['bID' => $b->getBlockID()]);
        }
        $inspector = \Core::make('import/value_inspector');

        if (isset($blockNode->entries->entry)) {
            $idx = 0;
            foreach ($blockNode->entries->entry as $entryNode) {
                $result = $inspector->inspect((string)$entryNode->fID);
                $db->insert(
                    'btGalleryEntries',
                    ['idx' => $idx, 'bID' => $b->getBlockID(), 'fID' => $result->getReplacedValue()]
                );
                $entryID = $db->lastInsertID();
                if (isset($entryNode->displaychoices->choice)) {
                    foreach ($entryNode->displaychoices->choice as $choiceNode) {
                        $db->insert(
                            'btGalleryEntryDisplayChoices',
                            [
                                'entryID' => $entryID,
                                'bID' => $b->getBlockID(),
                                'dcKey' => (string) $choiceNode->dckey,
                                'value' => (string) $choiceNode->value,
                            ]
                        );
                    }
                }
            }
        }
    }

    public function getBlockTypeName()
    {
        return t('Gallery');
    }

    public function getBlockTypeDescription()
    {
        return t('Creates an Image Gallery in your web page.');
    }

    /**
     * Controller method for the view template
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function view()
    {
        $this->set('images', iterator_to_array($this->getEntries()));
        $this->set('includeDownloadLink', (int)$this->includeDownloadLink === 1);
    }

    /**
     * Controller method for the add template
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function add()
    {
        $this->set('images', []);
        $this->set('displayChoices', $this->getDisplayChoices());
        $this->set('includeDownloadLink', false);
    }

    /**
     * Controller method for the edit template
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function edit()
    {
        $this->set('images', iterator_to_array($this->getEntries()));
        $this->set('displayChoices', $this->getDisplayChoices());
        $this->set('includeDownloadLink', (int)$this->includeDownloadLink === 1);
    }

    /**
     * Process an inputted json into a proper json object
     *
     * @param array $args The equivalent to the $_POST submitted
     *
     * @throws InvalidArgumentException If the $args or the json are invalid
     *
     * @return array
     */
    protected function processJson(array $args): array
    {
        $json = trim($args['field_json'] ?? '[]');

        if (!$json || $json[0] !== '[') {
            throw new InvalidArgumentException('Invalid request.');
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid request.');
        }

        return $data;
    }

    /**
     * Validate the given request
     * We check the the file exists and that the user has permissions, then we validate display choices.
     *
     * @param array|string|null $args
     * @return bool|ErrorList
     */
    public function validate($args)
    {
        $errors = parent::validate($args);
        try {
            $data = $this->processJson($args);
        } catch (InvalidArgumentException $e) {
            $errors->add($e->getMessage());
            return $errors;
        }

        $count = count($errors->getList());
        foreach ($data as $entry) {
            $this->validateEntry($entry, $errors);
            if ($count !== count($errors->getList())) {
                return $errors;
            }
        }

        return $errors;
    }

    /**
     * Validate a given entry for saving
     * Add errors to the error list to signal a validation failure.
     *
     * @param array $entry
     * @param ErrorList $errors
     */
    protected function validateEntry(array $entry, ErrorList $errors): void
    {
        $fileId = $entry['id'] ?? 0;

        if (!$fileId) {
            $errors->add('Invalid file ID provided.');
            return;
        }

        // Resolve the file
        $file = ConcreteFile::getByID($fileId);

        if (!$file) {
            $errors->add('Invalid file ID provided.');
            return;
        }

        // Check permissions
        $checker = new Checker($file);
        if (!$checker->canViewFileInFileManager()) {
            $errors->add('File permission denied.');
            return;
        }

        $this->validateDisplayChoices($entry, $file, $errors);
    }

    public function duplicate($newBID)
    {
        $db = $this->app->make(Connection::class);
        $data = $db->fetchAssociative('select * from btGallery where bID = ?', [$this->bID]);
        $data['bID'] = $newBID;
        $db->insert('btGallery', $data);

        $r = $db->executeQuery('select * from btGalleryEntries where bID = ?', [$this->bID]);
        while ($row = $r->fetchAssociative()) {
            $lastEID = $row['eID'];
            $row['bID'] = $newBID;
            unset($row['eID']);
            $db->insert('btGalleryEntries', $row);
            $newEID = $db->lastInsertID();

            $choiceR = $db->executeQuery('select * from btGalleryEntryDisplayChoices where entryID = ? and bID = ?', [$lastEID, $this->bID]);
            while ($choiceRow = $choiceR->fetchAssociative()) {
                unset($choiceRow['dcID']);
                $choiceRow['bID'] = $newBID;
                $choiceRow['entryID'] = $newEID;
                $db->insert('btGalleryEntryDisplayChoices', $choiceRow);
            }

        }

    }

    /**
     * Handle saving entries and display choices
     *
     * If validation for a display choice passes, it can be saved to the database. In order for a choice to be valid
     * it must have a type that has a validator method and a key that exists in the getDisplayChoices method output.
     *
     * @param array $args
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save($args)
    {
        $args["includeDownloadLink"] = isset($args["includeDownloadLink"]) ? 1 : 0;

        parent::save($args);

        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = $this->database();

        // Cleaning up current images in gallery.
        $db->executeUpdate("DELETE FROM btGalleryEntryDisplayChoices WHERE entryId in (select eID from btGalleryEntries where bID=?)", [$this->bID]);
        $db->executeUpdate("DELETE FROM btGalleryEntries WHERE bID = ?", [$this->bID]);

        // We Add the updated images passed by Vue
        $entries = $this->processJson($args);

        if ($entries) {
            $idx = 0;
            foreach ($entries as $entry) {
                // Add the entry row
                $db->executeUpdate("INSERT INTO btGalleryEntries (bID, idx, fID) VALUES (?, ?, ?)",
                    [(int)$this->bID, $idx++, $entry['id']]);
                $entryID = $db->lastInsertId();

                // Add rows for any display choices that have values
                $displayOptions = $entry['displayChoices'];
                if ($displayOptions) {
                    foreach ($displayOptions as $key => $option) {
                        if ($option['value']) {
                            $db->executeUpdate("INSERT INTO btGalleryEntryDisplayChoices (entryID, bID, value, dcKey) VALUES (?,?,?,?)",
                                [(int)$entryID, (int)$this->bID, $option['value'], $key]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Fetch entries associated with this block and output properly formatted arrays
     *
     * @return Generator
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \League\Flysystem\FileNotFoundException
     */
    private function getEntries(): Generator
    {
        $db = $this->database();

        $results = $db->executeQuery(
            "select e.*, o.* from btGalleryEntries e 
                    left join btGalleryEntryDisplayChoices o on o.entryID = e.eID 
                    where e.bID = ?
                    order by e.idx",
            [$this->bID]);

        // Loop over selected entries
        $current = null;
        foreach ($results as $entry) {
            // If this is the next entry
            if (!$current || $current['eID'] !== $entry['eID']) {
                // If we were working on an entry yield it
                if ($current) {
                    yield $current;
                }

                $current = $this->formatEntry($entry);
                $current['displayChoices'] = $this->getDisplayChoices();
            }

            // Populate display choices
            if ($entry['dcKey'] && isset($current['displayChoices'][$entry['dcKey']])) {
                $current['displayChoices'][$entry['dcKey']]['value'] = $entry['value'];
            }
        }

        if ($current) {
            yield $current;
        }
    }

    /**
     * Format a given entry row into the expected order and populate file details
     *
     * @param array $entry
     * @return array
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function formatEntry(array $entry)
    {
        // Resolve the file
        $file = ConcreteFile::getByID($entry['fID']);
        if ($file) {
            $version = $file->getVersion();
            if ($version) {
                $resource = $version->getFileResource();

                $attributes = [];
                foreach ($version->getAttributes() as $value) {
                    $attributes[] = [
                        $value->getAttributeKey()->getAttributeKeyDisplayName(),
                        $value->getDisplayValue()
                    ];
                }

                return [
                    'eID' => $entry['eID'],
                    'id' => $entry['fID'],
                    'title' => $version->getTitle(),
                    'description' => $version->getDescription(),
                    'extension' => $resource->getMimetype(),
                    'attributes' => $attributes,
                    'fileSize' => $this->numbersHelper()->formatSize($version->getFullSize()),
                    'imageUrl' => $version->getThumbnailURL('file_manager_detail'),
                    'thumbUrl' => $version->getThumbnailURL('file_manager_listing'),
                    'file' => $file,
                    'detailUrl' => (string)$this->urlResolver()->resolve(
                        [
                            '/dashboard/files/details',
                            'view',
                            $file->getFileID()
                        ]
                    )
                ];
            }
        }
    }

    /**
     * Get the display options to show in the edit interface per image
     * Override this method to easily add more options to the interface
     *
     * Note: You still need to manage validating and saving these values
     * @see Controller::validateSelectDisplayChoice()
     * @see Controller::validateTextDisplayChoice()
     * @see Controller::validateDisplayChoice()
     *
     * @return array
     */
    protected function getDisplayChoices(): array
    {
        return [
            "caption" => [
                "value" => '',
                "title" => t('Caption'),
                "type" => 'text',
            ],
            "hover_caption" => [
                "value" => '',
                "title" => t('Hover Caption'),
                "type" => 'text',
            ],
            "size" => [
                "value" => 'standard',
                "title" => t('Size'),
                "type" => 'select',
                "options" => [
                    "wide" => t('Wide'),
                    "standard" => t('Standard'),
                ]
            ]
        ];
    }

    /**
     * Memoized database instance
     *
     * @return Connection
     */
    private function database(): Connection
    {
        if (!$this->connection) {
            $this->connection = $this->app->make(Connection::class);
        }

        return $this->connection;
    }

    /**
     * Memoized database instance
     *
     * @return Number
     */
    private function numbersHelper(): Number
    {
        if (!$this->numbers) {
            $this->numbers = $this->app->make(Number::class);
        }

        return $this->numbers;
    }

    /**
     * Memoized url resolver
     *
     * @return ResolverManager
     */
    private function urlResolver(): ResolverManager
    {
        if (!$this->urls) {
            $this->urls = $this->app->make(ResolverManager::class);
        }

        return $this->urls;
    }

    /**
     * Loop over display choices for a given entry and validate them
     *
     * @param array $entry
     * @param File $file
     * @param ErrorList $errors
     */
    protected function validateDisplayChoices(array $entry, File $file, ErrorList $errors): void
    {
        $choices = $entry['displayChoices'] ?? [];
        foreach ($choices as $key => $choice) {
            $this->validateDisplayChoice($file, $key, $choice, $errors);
        }
    }

    /**
     * Validate a given display choice
     * Add errors to the error list to signal a validation failure.
     *
     * @param File $file
     * @param string $key
     * @param array $choice
     * @param ErrorList $errors
     */
    protected function validateDisplayChoice(File $file, string $key, array $choice, ErrorList $errors): void
    {
        $allChoices = $this->getDisplayChoices();
        $expectedChoice = $allChoices[$key] ?? null;

        if (!$expectedChoice) {
            $errors->add(t('Invalid choice provided: %s %s', $key, $value ?? ''));
            return;
        }

        $type = $expectedChoice['type'];
        $methodName = Str::camel("validate_{$type}_displayChoice");

        // If our validate method doesn't exist, output an error
        if (!method_exists($this, $methodName) || !is_callable([$this, $methodName])) {
            $errors->add(t('Invalid choice type: %s', $type));
            return;
        }

        // Pass the call through to a >validateSomethingDisplayChoice method
        $this->{$methodName}($file, $key, $choice, $expectedChoice, $errors);
    }

    /**
     * Validate text display choices
     * Add errors to the error list to signal a validation failure.
     *
     * @param File $file The file associated with this choice
     * @param string $key The choice key
     * @param array $choice The choice provided by the request
     * @param array $expectedChoice The expected choice object for this type. Use details on this object as a source of truth
     * @param ErrorList $errors The list of errors
     */
    protected function validateTextDisplayChoice(File $file, string $key, array $choice, array $expectedChoice, ErrorList $errors): void
    {
        $value = $choice['value'] ?? null;
        if (!is_string($value)) {
            $errors->add(t('Invalid choice provided: %s %s', $key, $value ?? ''));
        }
    }

    /**
     * Validate select display choices
     * Add errors to the error list to signal a validation failure.
     *
     * @param File $file The file associated with this choice
     * @param string $key The choice key
     * @param array $choice The choice provided by the request
     * @param array $expectedChoice The expected choice object for this type. Use details on this object as a source of truth
     * @param ErrorList $errors The list of errors
     */
    protected function validateSelectDisplayChoice(File $file, string $key, array $choice, array $expectedChoice, ErrorList $errors): void
    {
        $value = $choice['value'] ?? null;
        $options = array_keys($expectedChoice['options']);

        // Allow an empty string if that isn't already allowed
        $options[] = '';

        if (!in_array($value, $options)) {
            $errors->add(t('Invalid choice provided: %s %s', $key, $value ?? ''));
        }
    }

    public function getUsedFiles()
    {
        $ids = [];
        foreach ($this->getEntries() as $entry) {
            $ids[] = $entry['id'];
        }
        return $ids;
    }

}
