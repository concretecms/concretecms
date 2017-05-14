<?php
namespace Concrete\Block\Faq;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 465;
    protected $btTable = 'btFaq';
    protected $btExportTables = ['btFaq', 'btFaqEntries'];
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;

    public function getBlockTypeName()
    {
        return t('FAQ');
    }

    public function getBlockTypeDescription()
    {
        return t('Frequently Asked Questions Block');
    }

    public function getSearchableContent()
    {
        $content = '';
        $db = $this->app->make('database')->connection();
        $v = [$this->bID];
        $q = 'SELECT * FROM btFaqEntries WHERE bID = ?';
        $r = $db->executeQuery($q, $v);
        foreach ($r as $row) {
            $content .= $row['title'] . ' ' . $row['linkTitle'] . ' ' . $row['description'];
        }

        return $content;
    }

    public function edit()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->fetchAll('SELECT * FROM btFaqEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);
        $this->set('rows', $query);
    }

    public function view()
    {
        $db = $this->app->make('database')->connection();
        $query = $db->fetchAll('SELECT * FROM btFaqEntries WHERE bID = ? ORDER BY sortOrder', [$this->bID]);

        $rows = [];
        foreach ($query as $row) {
            $row['description'] = LinkAbstractor::translateFrom($row['description']);
            $rows[] = $row;
        }

        $this->set('rows', $rows);
    }

    public function duplicate($newBID)
    {
        $db = $this->app->make('database')->connection();
        $v = [$this->bID];
        $q = 'SELECT * FROM btFaqEntries WHERE bID = ?';
        $r = $db->executeQuery($q, $v);
        foreach ($r as $row) {
            $db->executeQuery(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) VALUES(?,?,?,?,?)',
                [
                    $newBID,
                    $row['title'],
                    $row['linkTitle'],
                    $row['description'],
                    $row['sortOrder'],
                ]
            );
        }
    }

    public function delete()
    {
        $db = $this->app->make('database')->connection();
        $db->executeQuery('DELETE FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        parent::delete();
    }

    public function save($args)
    {
        $db = $this->app->make('database')->connection();
        $db->executeQuery('DELETE FROM btFaqEntries WHERE bID = ?', [$this->bID]);
        parent::save($args);
        $count = isset($args['sortOrder']) ? count($args['sortOrder']) : 0;

        $i = 0;
        while ($i < $count) {
            if (isset($args['description'][$i])) {
                $args['description'][$i] = LinkAbstractor::translateTo($args['description'][$i]);
            }

            $db->executeQuery(
                'INSERT INTO btFaqEntries (bID, title, linkTitle, description, sortOrder) VALUES(?,?,?,?,?)',
                [
                    $this->bID,
                    $args['title'][$i],
                    $args['linkTitle'][$i],
                    $args['description'][$i],
                    $args['sortOrder'][$i],
                ]
            );
            ++$i;
        }
    }
}
