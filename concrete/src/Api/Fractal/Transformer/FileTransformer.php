<?php
namespace Concrete\Core\Api\Fractal\Transformer;

use Carbon\Carbon;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Api\Resources;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class FileTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'custom_attributes',
    ];

    /**
     * Basic transforming of a file entity into an array
     *
     * @param FileEntity $file
     * @return array
     */
    public function transform(FileEntity $file)
    {
        $version = $file->getApprovedVersion();
        $folderID = null;
        if ($fileFolder = $file->getFileFolderObject()) {
            $folderID = $fileFolder->getTreeNodeID();
        }
        $data['id'] = $file->hasFileUUID() ? $file->getFileUUID() : $file->getFileID();
        $data['title'] = $version->getTitle();
        $data['description'] = $version->getDescription();
        $data['tags'] = $version->getTags();
        $data['url'] = $version->getURL();
        $data['file_type'] = $version->getType();
        $data['extension'] = $version->getExtension();
        $data['tracked_url'] = (string) $version->getDownloadURL();
        $data['folder'] = $folderID;
        $data['date_added'] = Carbon::make($version->getDateAdded())->toAtomString();
        $data['size'] =
            [
                'description' => $version->getSize(),
                'exact' => $version->getFullSize(),
            ];
        return $data;
    }

    public function includeCustomAttributes(FileEntity $file)
    {
        $values = $file->getObjectAttributeCategory()->getAttributeValues($file);
        return new Collection($values, new AttributeValueTransformer(), Resources::RESOURCE_CUSTOM_ATTRIBUTES);
    }


}
