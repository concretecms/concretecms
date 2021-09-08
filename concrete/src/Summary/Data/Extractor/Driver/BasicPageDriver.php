<?php

namespace Concrete\Core\Summary\Data\Extractor\Driver;

use Concrete\Core\Page\Page;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Summary\Data\Extractor\Driver\Traits\GetThumbnailTrait;
use Concrete\Core\Summary\Data\Field\AuthorDataFieldData;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\DatetimeDataFieldData;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Field\LazyPageLinkDataFieldData;
use Concrete\Core\Summary\Data\Field\LinkDataFieldData;
use Concrete\Core\User\UserInfoRepository;

class BasicPageDriver implements DriverInterface
{

    /**
     * @var InstallationService
     */
    protected $installationService;

    public function __construct(InstallationService $installationService)
    {
        $this->installationService = $installationService;
    }

    use GetThumbnailTrait;

    public function getCategory()
    {
        return 'page';
    }

    public function isValidForObject($mixed): bool
    {
        return $mixed instanceof Page;
    }

    public function getThumbnailAttributeKeyHandle()
    {
        return 'thumbnail';
    }

    /**
     * @param $mixed Page
     * @return Collection
     */
    public function extractData(CategoryMemberInterface $mixed): Collection
    {
        $collection = new Collection();
        $collection->addField(new DataField(FieldInterface::FIELD_TITLE, $mixed->getCollectionName()));
        if ($this->installationService->isMultisiteEnabled()) {
            $collection->addField(new DataField(FieldInterface::FIELD_LINK, new LazyPageLinkDataFieldData()));
        } else {
            $collection->addField(
                new DataField(FieldInterface::FIELD_LINK, new LinkDataFieldData($mixed->getCollectionLink()))
            );
        }
        $collection->addField(
            new DataField(
                FieldInterface::FIELD_DATE, new DatetimeDataFieldData($mixed->getCollectionDatePublicObject())
            )
        );
        $description = $mixed->getCollectionDescription();
        if ($description) {
            $collection->addField(new DataField(FieldInterface::FIELD_DESCRIPTION, $description));
        }
        $thumbnail = $this->getThumbnailDataField($mixed);
        if ($thumbnail) {
            $collection->addField($thumbnail);
        }
        $repository = app(UserInfoRepository::class);
        $author = $repository->getByID($mixed->getCollectionUserID());
        if ($author) {
            $collection->addField(new DataField(FieldInterface::FIELD_AUTHOR, new AuthorDataFieldData($author)));
        }

        return $collection;
    }

}
