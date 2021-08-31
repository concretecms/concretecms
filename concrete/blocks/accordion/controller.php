<?php
namespace Concrete\Block\Accordion;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
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

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btInterfaceWidth = 720;
    protected $btInterfaceHeight = 580;
    protected $btTable = 'btAccordion';
    protected $btExportTables = ['btAccordion', 'btAccordionEntries'];
    protected $btWrapperClass = 'ccm-ui';
    protected $btDefaultSet = 'navigation';
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
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

    public function getSearchableContent()
    {
        $content = '';
        $db = $this->app->make('database')->connection();
        $v = [$this->bID];
        $q = 'SELECT * FROM btAccordionEntries WHERE bID = ? order by sortOrde';
        $r = $db->executeQuery($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['description'];
        }

        return $content;
    }

    public function edit()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->fetchAll('SELECT * FROM btAccordionEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);

        $entries = [];
        foreach ($query as $row) {
            $entry = new AccordionEntry($row['id'], $row['title'], LinkAbstractor::translateFromEditMode($row['description']));
            $entries[] = $entry;
        }

        $this->set('entries', $entries);
    }

    public function view()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->fetchAll('SELECT * FROM btAccordionEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);

        $entries = [];
        foreach ($query as $row) {
            $entry = new AccordionEntry($row['id'], $row['title'], LinkAbstractor::translateFrom($row['description']));
            $entries[] = $entry;
        }

        $this->set('entries', $entries);
    }

    public function duplicate($newBID)
    {
        $db = $this->app->make(Connection::class);
        $copyFields = 'title, description, sortOrder';
        $db->executeUpdate(
            "INSERT INTO btAccordionEntries (bID, {$copyFields}) SELECT ?, {$copyFields} FROM btAccordionEntries WHERE bID = ?",
            [
                $newBID,
                $this->bID,
            ]
        );
    }

    public function add()
    {
        $this->set('entries', []);
    }

    public function delete()
    {
        $db = $this->app->make('database')->connection();
        $db->executeQuery('DELETE FROM btAccordionEntries WHERE bID = ?', [$this->bID]);
        parent::delete();
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


    public function save($args)
    {
        parent::save($args);

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $db->executeStatement("DELETE FROM btAccordionEntries WHERE bID = ?", [$this->bID]);
        $entries = $this->processJson($args);

        if ($entries) {
            $sortOrder = 0;
            foreach ($entries as $entry) {
                // Add the entry row
                $db->executeStatement("INSERT INTO btAccordionEntries (bID, sortOrder, title, description) VALUES (?, ?, ?, ?)",
                                   [(int)$this->bID, $sortOrder++, $entry['title'], $entry['description']]);
            }
        }
    }

}
