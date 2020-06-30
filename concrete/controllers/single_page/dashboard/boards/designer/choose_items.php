<?php
namespace Concrete\Controller\SinglePage\Dashboard\Boards\Designer;

use Concrete\Core\Board\Command\ResetBoardCustomWeightingCommand;
use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\SanitizeService;

class ChooseItems extends DashboardSitePageController
{

    public function view($id = null)
    {
        $element = $this->getCustomElement($id);
        if (is_object($element)) {
            $this->set('element', $element);
        } else {
            return $this->redirect('/dashboard/boards/designer');
        }
    }

    /**
     * @param $id
     * @return CustomElement
     */
    protected function getCustomElement($id)
    {
        $r = $this->entityManager->getRepository(CustomElement::class);
        $element = $r->findOneById($id);
        return $element;
    }

    public function submit($elementID = null)
    {
        $element = $this->getCustomElement($elementID);
        if (is_object($element)) {
            $this->set('element', $element);
        }
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            // @TODO - This should not be hard coded. We should be able to pick these out of the request
            // but the signature of these drivers isn't set in stone yet, and I don't want to have
            // third party developers build toward it only to have it change. So right now this is hard coded to
            // events and pages.
            $request = $this->request->request->all();
            $pages = [];
            $events = [];
            if (!empty($request['field']['page'])) {
                foreach((array) $request['field']['page'] as $cID) {
                    $page = Page::getByID($cID);
                    if ($page && !$page->isError()) {
                        $pages[] = $page;
                    }
                }
            }
            $eventOccurrenceService = $this->app->make(EventOccurrenceService::class);
            if (!empty($request['field']['calendar_event'])) {
                foreach((array) $request['field']['calendar_event'] as $eventOccurrenceID) {
                    $occurrence = $eventOccurrenceService->getByID($eventOccurrenceID);
                    if ($occurrence) {
                        $events[] = $occurrence;
                    }
                }
            }
        }

        $this->view($elementID);
    }
    /*
    public function add($boardID = null, $dataSourceID = null)
    {
        $board = $this->getBoard($boardID);
        $dataSource = $this->entityManager->find(DataSource::class, $dataSourceID);
        if (is_object($board) && $dataSource) {
            $this->set('board', $board);
            $this->set('dataSource', $dataSource);
            $this->render('/dashboard/boards/data_sources/add');
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function update($configuredDataSourceID = null)
    {
        $configuredDataSource = $this->entityManager->find(ConfiguredDataSource::class, $configuredDataSourceID);
        if ($configuredDataSource) {
            $board = $configuredDataSource->getBoard();
            $dataSource = $configuredDataSource->getDataSource();
            $this->set('board', $board);
            $this->set('dataSource', $dataSource);
            $this->set('configuredDataSource', $configuredDataSource);
            $this->render('/dashboard/boards/data_sources/update');
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function update_data_source($configuredDataSourceID = null)
    {
        $configuredDataSource = $this->entityManager->find(ConfiguredDataSource::class, $configuredDataSourceID);
        if ($configuredDataSource) {
            $board = $configuredDataSource->getBoard();
            $dataSource = $configuredDataSource->getDataSource();
            if (!$this->token->validate('update_data_source')) {
                $this->error->add($this->token->getErrorMessage());
            }
            $security = new SanitizeService();
            $strings = $this->app->make(Strings::class);
            $name = $security->sanitizeString($this->post('dataSourceName'));

            if (!$strings->notempty($name)) {
                $this->error->add(t('You must specify a valid name for your data source.'));
            }
            if (!$this->error->has()) {
                $driver = $dataSource->getDriver();
                $saver = $driver->getSaver();
                $saver->updateConfiguredDataSourceFromRequest($name, $configuredDataSource, $this->request);
                $this->flash('success', t('Data Source updated successfully.'));
                $resetCommand = new ResetBoardCustomWeightingCommand();
                $resetCommand->setBoard($board);
                $this->executeCommand($resetCommand);
                return $this->redirect('/dashboard/boards/data_sources', 'view', $board->getBoardID());
            }
            $this->update($board->getBoardID(), $dataSource->getId());
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }

    public function delete_data_source($configuredDataSourceID = null)
    {
        $configuredDataSource = $this->entityManager->find(ConfiguredDataSource::class, $configuredDataSourceID);
        if ($configuredDataSource) {
            if (!$this->token->validate('delete_data_source')) {
                $this->error->add($this->token->getErrorMessage());
            }
            if (!$this->error->has()) {
                $board = $configuredDataSource->getBoard();
                $this->entityManager->remove($configuredDataSource);
                $this->entityManager->flush();
                $resetCommand = new ResetBoardCustomWeightingCommand();
                $resetCommand->setBoard($board);
                $this->executeCommand($resetCommand);

                $this->flash('success', t('Data Source removed successfully.'));
                return $this->redirect('/dashboard/boards/data_sources', 'view', $board->getBoardID());
            }
            $this->update($configuredDataSource->getId());
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }
    }


    public function add_data_source($boardID = null, $dataSourceID = null)
    {
        $board = $this->getBoard($boardID);
        $dataSource = $this->entityManager->find(DataSource::class, $dataSourceID);
        if (is_object($board) && $dataSource) {
            if (!$this->token->validate('add_data_source')) {
                $this->error->add($this->token->getErrorMessage());
            }
            $security = new SanitizeService();
            $strings = $this->app->make(Strings::class);
            $name = $security->sanitizeString($this->post('dataSourceName'));

            if (!$strings->notempty($name)) {
                $this->error->add(t('You must specify a valid name for your data source.'));
            }

            if (!$this->error->has()) {
                $driver = $dataSource->getDriver();
                $saver = $driver->getSaver();
                $saver->addConfiguredDataSourceFromRequest($name, $board, $dataSource, $this->request);
                $resetCommand = new ResetBoardCustomWeightingCommand();
                $resetCommand->setBoard($board);
                $this->executeCommand($resetCommand);

                $this->flash('success', t('Data Source created successfully.'));
                return $this->redirect('/dashboard/boards/data_sources', 'view', $board->getBoardID());
            }
            $this->add($boardID, $dataSourceID);
        } else {
            return $this->redirect('/dashboard/boards/boards');
        }

    }
    */


}
