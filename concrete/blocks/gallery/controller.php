<?php

namespace Concrete\Block\Gallery;

use Concrete\Core\Block\BlockController;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\Support\Facade\Database;

class Controller extends BlockController
{
    protected $btTable = 'btGallery';
    protected $btInterfaceWidth = '800';
    protected $btInterfaceHeight = '820';
    protected $btExportTables = ['btGallery', 'btGalleryEntries', 'btGalleryEntryDisplayChoices'];
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeName()
    {
        return t('Gallery');
    }

    public function getBlockTypeDescription()
    {
        return t('Creates an Image Gallery in your web page.');
    }

    public function view()
    {
        $entries = $this->getEntries();
        $images = $this->getImages($entries);
        $this->set('images', $images);
    }

    public function add()
    {
        $entries = [
            0 => ['fID' => 1],
            1 => ['fID' => 2],
            2 => ['fID' => 3],
            3 => ['fID' => 4],
            4 => ['fID' => 5]
        ];
        $this->set('json', $this->getImages($entries));
    }

    public function edit()
    {
        $entries = $this->getEntries();
        $this->set('json', $this->getImages($entries));
    }

    public function save($args)
    {
        parent::save($args);

        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = Database::connection();

        //Cleaning up current images in gallery.
        $db->query("DELETE FROM btGalleryEntries WHERE bID = ? ", [(int)$this->bID]);
        $db->query("DELETE FROM btGalleryEntryDisplayChoices WHERE bID = ? ", [(int)$this->bID]);

        //We Add the updated images passed by Vue
        $entries = json_decode($args['field_json']);
        if ($entries) {
            foreach ($entries as $entry) {
                $displayOptions = $entry->displayChoices;
                $db->query("INSERT INTO btGalleryEntries (bID, idx, fID) VALUES (?, 0, ?)",
                    [(int)$this->bID, $entry->id]);
                $entryID = $db->lastInsertId();
                if ($displayOptions) {
                    foreach ($displayOptions as $key => $option) {
                        if (!empty($option->value)) {
                            $db->query("INSERT INTO btGalleryEntryDisplayChoices (entryID, bID, value, dcKey) VALUES (?,?,?,?)",
                                [(int)$entryID, (int)$this->bID, $option->value, $key]);
                        }
                    }
                }
            }
        }
    }

    private function getEntries()
    {
        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = Database::connection();

        return $db->fetchAll("SELECT eID, fID FROM btGalleryEntries WHERE bID = ? ", [(int)$this->bID]);
    }

    private function getDisplayOptions($entryID)
    {
        //We will be adding logic here, but for now we are hard coding the displayOptions
        $displayOptions = [
            "gallery-specific-options" => [
                "value" => '',
                "title" => 'Gallery Specific Options',
                "type" => 'text'
            ],
            "size" => [
                "value" => '',
                "title" => 'Size',
                "type" => 'select',
                "options" => [
                    "square" => 'Square Image',
                    "default" => 'Keep Image Aspect Ratio'
                ]
            ]
        ];

        if ($entryID) {
            /** @var \Concrete\Core\Database\Connection\Connection $db */
            $db = Database::connection();
            $entryDisplayOptions = $db->fetchAll("SELECT * FROM btGalleryEntryDisplayChoices WHERE entryID = ?",
                [(int)$entryID]);

            if ($entryDisplayOptions) {
                foreach ($entryDisplayOptions as $option) {
                    $displayOptions[$option['dcKey']]['value'] = $option['value'];
                }
            }
        }

        return $displayOptions;
    }

    /**
     * @param $entries
     *
     * @return array
     */
    private function getImages($entries)
    {
        $images = [];

        if (!empty($entries)) {
            foreach ($entries as $entry) {
                /** @var Concrete\Core\Entity\File\File $file */
                $file = \File::getByID($entry['fID']);

                /** @var Concrete\Core\Entity\File\Version $fv */
                $fv = $file->getVersion();

                /** @var League\Flysystem\File $resource */
                $resource = $file->getFileResource();

                /** @var Concrete\Core\Entity\File\Image\Thumbnail\Type\Type $thumbnailImage */
                $thumbnailImage = ThumbnailType::getByHandle('file_manager_listing');
                $imageDetail = ThumbnailType::getByHandle('file_manager_detail');

                $title = $fv->getTitle();
                $description = $fv->getDescription();
                $size = $fv->getFullSize();
                $size = $this->app->make('helper/number')->formatSize($size);
                $fileType = $resource->getMimetype();
                $thumbnail = $file->getThumbnailURL($thumbnailImage->getBaseVersion());
                $imageURL = $file->getThumbnailURL($imageDetail->getBaseVersion());

                $images[] = [
                    "id" => $entry['fID'],
                    "title" => $title,
                    "description" => $description,
                    "extension" => $fileType,
                    "attributes" => [],
                    "fileSize" => $size,
                    "imageUrl" => $imageURL,
                    "thumbUrl" => $thumbnail,
                    "detailUrl" => '',
                    "displayChoices" => $this->getDisplayOptions($entry['eID'])
                ];
            }
        }
        return $images;
    }
}
