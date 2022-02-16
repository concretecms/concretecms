<?php

namespace Concrete\Block\Faq;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 600;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 465;

    /**
     * @var string
     */
    protected $btTable = 'btFaq';

    /**
     * @var string[]
     */
    protected $btExportTables = ['btFaq', 'btFaqEntries'];

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

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

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('FAQ');
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Frequently Asked Questions Block');
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::FAQ,
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception|\Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
    public function getSearchableContent()
    {
        $content = '';
        $qb = $this->getStandardQuery();
        $r = $qb->execute()->fetchAllAssociative();
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['linkTitle'] . ' ' . $row['description'];
        }

        return $content;
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $qb = $this->getStandardQuery();
        $results = $qb->execute()->fetchAllAssociative();

        $rows = [];
        foreach ($results as $result) {
            $result['description'] = LinkAbstractor::translateFromEditMode($result['description']);
            $rows[] = $result;
        }

        $this->set('rows', $rows);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $qb = $this->getStandardQuery();
        $results = $qb->execute()->fetchAllAssociative();

        $rows = [];
        foreach ($results as $result) {
            $result['description'] = LinkAbstractor::translateFrom($result['description']);
            $rows[] = $result;
        }

        $this->set('rows', $rows);
    }

    /**
     * @param int $newBID
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Concrete\Core\Legacy\BlockRecord|void|null
     */
    public function duplicate($newBID)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $copyFields = 'title, linkTitle, description, sortOrder';
        $db->executeStatement(
            "INSERT INTO btFaqEntries (bID, {$copyFields}) SELECT ?, {$copyFields} FROM btFaqEntries WHERE bID = ?",
            [
                $newBID,
                $this->bID,
            ]
        );
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function delete()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $db->delete('btFaqEntries', ['bID' => $this->bID]);
        parent::delete();
    }

    /**
     * @param array<string, mixed> $args
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function save($args)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $db->delete('btFaqEntries', ['bID' => $this->bID]);
        parent::save($args);
        $count = isset($args['sortOrder']) ? count($args['sortOrder']) : 0;

        $i = 0;
        while ($i < $count) {
            if (isset($args['description'][$i])) {
                $args['description'][$i] = LinkAbstractor::translateTo($args['description'][$i]);
            }
            $db->insert('btFaqEntries', ['bID' => $this->bID,
                'title' => $args['title'][$i], 'linkTitle' => $args['linkTitle'][$i], 'description' => $args['description'][$i], 'sortOrder' => $args['sortOrder'][$i],
            ], [Types::INTEGER, Types::STRING, Types::STRING, Types::STRING, Types::INTEGER]);
            $i++;
        }
    }

    /**
     * Function for getting this blocks standard query.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return QueryBuilder
     */
    protected function getStandardQuery(): QueryBuilder
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb->select('*')->from('btFaqEntries')->where($qb->expr()->eq('bID', ':blockID'))->setParameter('blockID', $this->bID, Types::INTEGER)->orderBy('sortOrder');

        return $qb;
    }
}
