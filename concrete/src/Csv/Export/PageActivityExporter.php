<?php
namespace Concrete\Core\Csv\Export;

use Concrete\Core\Page\Page;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Localization\Service\Date;
use League\Csv\Writer;

class PageActivityExporter extends AbstractExporter
{
    /**
     * @var \Concrete\Core\Localization\Service\Date
     */
    protected $dateService;

    /**
     * Initialize the instance.
     *
     * A dummy category is used to prevent that all kind of page
     * attributes values are added to the CSV as well.
     *
     * @param \League\Csv\Writer $writer
     * @param \Concrete\Core\Localization\Service\Date $dateService
     */
    public function __construct(Writer $writer, Date $dateService)
    {
        parent::__construct($writer);

        $this->dateService = $dateService;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Csv\Export\AbstractExporter::getStaticHeaders()
     */
    protected function getStaticHeaders()
    {
        yield 'pageId';
        yield 'pagePath';
        yield 'pageName';
        yield 'versionId';
        yield 'versionDate';
        yield 'versionComments';
        yield 'versionAuthorUsername';
        yield 'versionApproverUsername';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Csv\Export\AbstractExporter::getStaticFieldValues()
     */
    protected function getStaticFieldValues(ObjectInterface $version)
    {
        /** @var \Concrete\Core\Page\Collection\Version\Version $version */

        /** @var \Concrete\Core\Page\Page $page */
        $page = Page::getByID($version->getCollectionID());

        yield (int) $page->getCollectionID();
        yield (string) $page->getCollectionPath();
        yield (string) $page->getCollectionName();
        yield (int) $version->getVersionID();
        yield (string) $this->getLocalizedDate($version->getVersionDateApproved());
        yield (string) $version->getVersionComments();
        yield (string) $version->getVersionAuthorUserName();
        yield (string) $version->getVersionApproverUserName();
    }

    /**
     * Converts a system string date to a (localized) app string date.
     *
     * @param string|null $value E.g. '2018-21-31 23:59:59'
     *
     * @return string|null
     */
    private function getLocalizedDate($value = null)
    {
        if ($value) {
            $value = $this->dateService
                ->toDateTime($value, 'app')
                ->format(Date::DB_FORMAT);
        }

        return $value;
    }
}
