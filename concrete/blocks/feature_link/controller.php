<?php

namespace Concrete\Block\FeatureLink;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Api\Block\ApiValueSchemaFactory;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Controller\SaveMode;
use Concrete\Core\Block\ExportDeclarations;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Html\Service\FontAwesomeIcon;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Theme\Theme;
use HtmlObject\Link;
use Concrete\Core\File\Tracker\RichTextExtractor;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, FileTrackableInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $body;

    /**
     * @var string|null
     */
    public $buttonText;

    /**
     * @var string|null
     */
    public $buttonExternalLink;

    /**
     * @var int|string|null
     */
    public $buttonInternalLinkCID;

    /**
     * @var int|string|null
     */
    public $buttonFileLinkID;

    /**
     * @var string|null
     */
    public $buttonColor;

    /**
     * @var string|null
     */
    public $buttonStyle;

    /**
     * @var string|null
     */
    public $buttonSize;

    /**
     * @var string|null
     */
    public $titleFormat;

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var int|string|null
     */
    public $fID;

    public $helpers = ['form'];

    public $buttonIcon;

    protected $btDefaultSet = 'basic';
    protected $btInterfaceWidth = 640;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btFeatureLink';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btExportFileColumns = ['buttonFileLinkID', 'fID'];
    protected $btExportPageColumns = ['buttonInternalLinkCID'];
    protected $btExportContentColumns = ['body'];
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputOnEditMode = true;
    protected $btCacheBlockOutputLifetime = 300;

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeName()
    {
        return t('Feature Link');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockTypeDescription()
    {
        return t('Add a title, body and a button/link to a page. Useful for calling out important links.');
    }

    /**
     * @return string[]
     */
    protected function getImageLinkPickers()
    {
        return [
            'none',
            'page',
            'file',
            'external_url' => ['maxlength' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS,
        ];
    }

    public function add()
    {
        $this->set('titleFormat', 'h2');
        $this->edit();
        $this->set('bf', null);
    }

    public function edit()
    {
        $theme = Theme::getSiteTheme();
        $this->set('editor', $this->app->make('editor'));
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('imageLinkPickers', $this->getImageLinkPickers());
        $this->set('themeColorCollection', $theme->getColorCollection());
        if ($this->buttonInternalLinkCID) {
            $this->set('imageLinkHandle', 'page');
            $this->set('imageLinkValue', $this->buttonInternalLinkCID);
        } elseif ($this->buttonFileLinkID) {
            $this->set('imageLinkHandle', 'file');
            $this->set('imageLinkValue', $this->buttonFileLinkID);
        } elseif ((string) $this->buttonExternalLink !== '') {
            $this->set('imageLinkHandle', 'external_url');
            $this->set('imageLinkValue', $this->buttonExternalLink);
        } else {
            $this->set('imageLinkHandle', 'none');
            $this->set('imageLinkValue', null);
        }
         // Image file object
         $bf = null;
         if ($this->getFileID() > 0) {
             $bf = $this->getFileObject();
         }
         $this->set('bf', $bf);
    }

    /**
     * @return bool
     */
    public function isComposerControlDraftValueEmpty()
    {
        $f = $this->getFileObject();
        if (is_object($f) && $f->getFileID()) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getFileID()
    {
        return isset($this->record->fID) ? $this->record->fID : (isset($this->fID) ? $this->fID : null);
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileObject()
    {
        return File::getByID($this->getFileID());
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getImageFeatureDetailFileObject()
    {
        // i don't know why this->fID isn't sticky in some cases, leading us to query
        // every damn time
        $db = $this->app->make('database')->connection();

        $file = null;
        $fID = $db->fetchColumn('SELECT fID FROM btContentImage WHERE bID = ?', [$this->bID], 0);
        if ($fID) {
            $f = File::getByID($fID);
            if (is_object($f) && $f->getFileID()) {
                $file = $f;
            }
        }

        return $file;
    }

    /**
     * @TODO - move all this logic into the DestinationPicker somehow. Make the destination picker save
     * its object into some kind of special destination object. Refactor destinationpicker into
     * vue component.
     *
     * @return string
     */
    public function getLinkURL()
    {
        $linkUrl = '';
        if (!empty($this->buttonExternalLink)) {
            $sec = $this->app->make('helper/security');
            $linkUrl = $sec->sanitizeURL($this->buttonExternalLink);
        } elseif (!empty($this->buttonInternalLinkCID)) {
            $linkToC = Page::getByID($this->buttonInternalLinkCID);
            if (is_object($linkToC) && !$linkToC->isError()) {
                $linkUrl = $linkToC->getCollectionLink();
            }
        } elseif (!empty($this->buttonFileLinkID)) {
            $fileLinkObject = File::getByID($this->buttonFileLinkID);
            if (is_object($fileLinkObject)) {
                $linkUrl = $fileLinkObject->getRelativePath();
            }
        }

        return $linkUrl;
    }

    public function view()
    {
      if ($this->buttonText || $this->getLinkURL()) {

        $button = new Link($this->getLinkURL(), $this->buttonText);
        $this->set('button', $button);

        $theme = Theme::getSiteTheme();
        if ($theme && $theme->supportsFeature(Features::TYPOGRAPHY)) {
          $this->set('theme', $theme);
        }

        $this->set('button', $button);
        $this->set('linkURL', $this->getLinkURL());
        $this->set('buttonIcon', $this->icon);
        $this->set('iconTag', FontAwesomeIcon::getFromClassNames(h($this->icon)));
      }
      // Check for a valid File in the view
      $f = $this->getFileObject();
      $this->set('f', $f);
    }

    public function save($args)
    {
        $fromCIF = $this->saveMode === SaveMode::SAVE_MODE_IMPORT;
        if (!$fromCIF) {
            list($imageLinkType, $imageLinkValue) = $this->app->make(DestinationPicker::class)->decode('imageLink', $this->getImageLinkPickers(), null, null, $args);
            $args['buttonInternalLinkCID'] = $imageLinkType === 'page' ? $imageLinkValue : 0;
            $args['buttonFileLinkID'] = $imageLinkType === 'file' ? $imageLinkValue : 0;
            $args['buttonExternalLink'] = $imageLinkType === 'external_url' ? $imageLinkValue : '';
        }
        $security = $this->app->make('helper/security');
        $args['icon'] = $security->sanitizeString($args['icon'] ?? '');
        $args = $args + [
            'fID' => 0,
        ];
        $args['fID'] = $args['fID'] != '' ? $args['fID'] : 0;
        parent::save($args);
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
                'icon' => [
                    'type' => ['string', 'null'],
                    'description' => 'The class names of the Font Awesome icon displayed in the button (for instance "fas fa-address-card").',
                ],
                'fID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_FILE, [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The image displayed above the title (0 for none).',
                ]),
                'title' => [
                    'type' => ['string', 'null'],
                    'description' => 'The title of the block.',
                ],
                'titleFormat' => [
                    'type' => 'string',
                    'enum' => array_keys(BlockController::$btTitleFormats),
                    'default' => 'h2',
                    'description' => 'The HTML element wrapping the title.',
                ],
                'body' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_CONTENT, [
                    'type' => ['string', 'null'],
                    'description' => 'The rich text displayed below the title.',
                ]),
                'buttonText' => [
                    'type' => ['string', 'null'],
                    'description' => 'The text of the button (when it\'s empty, and the button links to nothing, no button is displayed).',
                ],
                'buttonInternalLinkCID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_PAGE, [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The page the button links to (0 for none).',
                ]),
                'buttonFileLinkID' => $schemaFactory->describeReference(ExportDeclarations::REFERENCE_FILE, [
                    'type' => ['string', 'integer', 'null'],
                    'description' => 'The file the button links to (0 for none): it\'s used only when the button links to no page.',
                ]),
                'buttonExternalLink' => [
                    'type' => ['string', 'null'],
                    'maxLength' => 255,
                    'description' => 'The URL the button links to: it\'s used only when the button links to no page and to no file.',
                ],
                'buttonSize' => [
                    'type' => ['string', 'null'],
                    'enum' => ['', 'lg', 'sm'],
                    'description' => 'The size of the button: regular, large or small.',
                ],
                'buttonStyle' => [
                    'type' => ['string', 'null'],
                    'enum' => ['', 'outline', 'link'],
                    'description' => 'How the button is drawn: regular, outlined, or as a plain link.',
                ],
                'buttonColor' => [
                    'type' => ['string', 'null'],
                    'description' => 'The color of the button, which is one of the colors of the theme of the site.',
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
        if ($this->bID) {
            // the save() method resets the settings that it doesn't receive: let's keep the current ones
            $value += $this->serializeValueForApi();
        }

        return parent::getImportDataFromApiValue($page, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    public function getImportData($blockNode, $page)
    {
        $this->saveMode = SaveMode::SAVE_MODE_IMPORT;
        $args = parent::getImportData($blockNode, $page);
        foreach (['buttonInternalLinkCID', 'buttonFileLinkID', 'fID'] as $field) {
            $args[$field] = empty($args[$field]) ? 0 : (int) $args[$field];
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
        $result = $this->app->make(RichTextExtractor::class)->extractFiles($this->body);
        if (($fID = (int) $this->buttonFileLinkID) > 0) {
            $result[] = $fID;
        }
        if (($fID = (int) $this->fID) > 0) {
            $result[] = $fID;
        }

        return $result;
    }

    public function getSearchableContent()
    {
        return "{$this->title} {$this->body}";
    }
}
