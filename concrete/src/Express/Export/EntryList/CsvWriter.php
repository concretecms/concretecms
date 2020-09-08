<?php

namespace Concrete\Core\Express\Export\EntryList;

use Concrete\Core\Attribute\MulticolumnTextExportableAttributeInterface;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Site\Service;
use Doctrine\ORM\EntityManager;
use League\Csv\Writer;

/**
 * A Writer class for Express Entry objects
 */
class CsvWriter
{

    /** @var Writer The writer we use to output */
    protected $writer;

    /**
     * @var Date
     */
    protected $dateFormatter;

    /**
     * @var string
     */
    private $datetime_format;

    /**
     * @var Service|null
     */
    private $siteService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Writer $writer, Date $dateFormatter, EntityManager $entityManager, $datetime_format = DATE_ATOM)
    {
        $this->writer = $writer;
        $this->dateFormatter = $dateFormatter;
        $this->entityManager = $entityManager;
        $this->datetime_format = $datetime_format;
    }

    public function insertHeaders(Entity $entity)
    {
        $this->writer->insertOne(iterator_to_array($this->getHeaders($entity)));
    }

    /**
     * Insert all data from the passed EntryList
     * @param \Concrete\Core\Express\EntryList $list
     */
    public function insertEntryList(EntryList $list)
    {
        $list = clone $list;
        $this->writer->insertAll($this->projectList($list));
    }

    /**
     * A generator that takes an EntryList and converts it to CSV rows
     * @param \Concrete\Core\Express\EntryList $list
     * @return \Generator
     */
    private function projectList(EntryList $list)
    {
        $headers = array_keys(iterator_to_array($this->getHeaders($list->getEntity())));
        $statement = $list->deliverQueryObject()->execute();

        $total = 0;
        foreach ($statement as $result) {
            if ($entry = $list->getResult($result)) {
                yield $this->orderedEntry(iterator_to_array($this->projectEntry($entry)), $headers);
            }
            $total++;
            if ($total > 100) {
                $this->entityManager->clear();
                $total = 0;
            }
        }
    }

    /**
     * Return an entry in proper order
     * @param array $entry
     * @param array $headerKeys
     *
     * @return array
     */
    private function orderedEntry(array $entry, array $headerKeys)
    {
        $result = [];

        foreach ($headerKeys as $key) {
            $result[$key] = $entry[$key];
        }

        return $result;
    }

    /**
     * Turn an Entry into an array
     * @param \Concrete\Core\Entity\Express\Entry $entry
     * @return array
     */
    private function projectEntry(Entry $entry)
    {
        $date = $entry->getDateCreated();
        yield 'ccm_date_created' => $date ? $this->dateFormatter->formatCustom($this->datetime_format, $date) : null;

        $date = $entry->getDateModified();
        yield 'ccm_date_modified' => $date ? $this->dateFormatter->formatCustom($this->datetime_format, $date) : null;

        yield 'publicIdentifier' => $entry->getPublicIdentifier();

        // Resolve the site
        $site = $this->getSiteService()->getSiteByExpressResultsNodeID($entry->getResultsNodeID());
        yield 'site' => $site instanceof Site ? $site->getSiteHandle() : null;

        $author = $entry->getAuthor();
        if ($author) {
            yield 'author_name' => $author->getUserInfoObject()->getUserDisplayName();
        } else {
            yield 'author_name' => null;
        }

        $attributes = $entry->getAttributes();
        foreach ($attributes as $attribute) {
            $handle = $attribute->getAttributeKey()->getAttributeKeyHandle();

            // First yield out the plain text value
            yield $handle => $attribute->getPlainTextValue();

            // Next check for any multi-column values
            $controller = $attribute->getController();
            if ($controller instanceof MulticolumnTextExportableAttributeInterface) {
                $headers = $controller->getAttributeTextRepresentationHeaders();
                foreach ($controller->getAttributeValueTextRepresentation() as $key => $value) {
                    $header = $headers[$key];
                    yield "{$handle}.{$header}" => $value;
                }
            }
        }

        $associations = $entry->getAssociations();
        foreach ($associations as $association) {
            $output = [];
            if ($collection = $association->getSelectedEntries()) {
                foreach($collection as $entry) {
                    $output[] = $entry->getPublicIdentifier();
                }
            }
            yield $association->getAssociation()->getId() => implode('|', $output);
        }

    }

    /**
     * A generator that returns all headers
     * @param \Concrete\Core\Entity\Express\Entity $entity
     * @return \Generator
     */
    private function getHeaders(Entity $entity)
    {
        yield 'publicIdentifier' => 'publicIdentifier';
        yield 'ccm_date_created' => 'dateCreated';
        yield 'ccm_date_modified' => 'dateModified';
        yield 'site' => 'site';
        yield 'author_name' => 'authorName';

        $attributes = $entity->getAttributes();
        /** @var ExpressKey $attribute */
        foreach ($attributes as $attribute) {
            $name = $attribute->getAttributeKeyDisplayName();
            $handle = $attribute->getAttributeKeyHandle();

            // First yield out the main attribute key
            yield $handle => $name;

            // Next check for multi-column values
            $controller = $attribute->getController();
            if ($controller instanceof MulticolumnTextExportableAttributeInterface) {
                foreach ($controller->getAttributeTextRepresentationHeaders() as $subheader) {
                    yield "{$handle}.{$subheader}" => "{$name} - {$subheader}";
                }
            }
        }

        $associations = $entity->getAssociations();
        foreach ($associations as $association) {
            yield $association->getId() => $association->getTargetPropertyName();
        }
    }

    /**
     * Get the site service instance to use
     *
     * @return Service
     */
    protected function getSiteService(): Service
    {
        if (!$this->siteService) {
            $this->siteService = app(Service::class);
        }

        return $this->siteService;
    }

    /**
     * Override the site service
     *
     * @param Service $siteService
     */
    public function setSiteService(Service $siteService): void
    {
        $this->siteService = $siteService;
    }

}
