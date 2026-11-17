<?php
namespace Concrete\Controller\SinglePage\Dashboard\Files;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\File\Set\SetList as FileSetList;
use Concrete\Core\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Search\ColumnSet\Column\FileSetDisplayOrderColumn;
use Concrete\Core\File\Search\SearchProvider;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use FileSet;
use Permissions;
use PermissionKey;
use Loader;
use Exception;

class Sets extends DashboardPageController
{
    public $helpers = array('form', 'validation/token', 'concrete/ui');

    public function view()
    {
        $fsl = new FileSetList();
        if (isset($_REQUEST['fsKeywords'])) {
            $fsl->filterByKeywords($_REQUEST['fsKeywords']);
        }
        if (isset($_REQUEST['fsType'])) {
            $fsl->filterByType($_REQUEST['fsType']);
            $this->set('fsType', $_REQUEST['fsType']);
        } else {
            $fsl->filterByType(FileSet::TYPE_PUBLIC);
            $this->set('fsType', FileSet::TYPE_PUBLIC);
        }
        $fileSets = $fsl->getPage();
        $this->set('fileSets', $fileSets);
        $this->set('fsl', $fsl);
    }

    public function file_set_added()
    {
        $this->set('message', t('New file set added successfully.'));
        $this->view();
    }

    public function file_set_deleted()
    {
        $this->set('message', t('File set deleted successfully.'));
        $this->view();
    }

    public function delete($fsID, $token = '')
    {
        $fs = FileSet::getByID($fsID);

        $valt = Loader::helper('validation/token');
        if (!$valt->validate('delete_file_set', $token)) {
            throw new Exception($valt->getErrorMessage());
        }

        $fs->delete();
        $this->redirect('/dashboard/files/sets', 'file_set_deleted');
    }

    public function view_detail($fsID, $action = false)
    {
        $fs = FileSet::getByID($fsID);
        $this->set('fs', $fs);
        if ($action == 'file_set_updated') {
            $this->set('message', t('File set updated successfully.'));
        }
        $this->view();

        $provider = $this->app->make(SearchProvider::class);
        $options = array_map('intval', $provider->getItemsPerPageOptions());
        $itemsPerPage = (int) $provider->getItemsPerPage();
        $requested = $this->request->query->all()['itemsPerPage'] ?? null;
        if (is_scalar($requested) && in_array((int) $requested, $options, true)) {
            $itemsPerPage = (int) $requested;
        }

        $list = new FileList();
        $list->filterBySet($fs);
        $list->disableAutomaticSorting();
        $list->sortBySearchColumn(new FileSetDisplayOrderColumn($fs));
        $list->getQueryObject()->addOrderBy('f.fID', 'asc');
        $list->setItemsPerPage($itemsPerPage);
        $factory = new PaginationFactory($this->request);
        /** @var Pagination $pagination */
        $pagination = $factory->createPaginationObject($list, PaginationFactory::PERMISSIONED_PAGINATION_STYLE_PAGER);
        $detailURL = Url::to($this->getPageObject(), 'view_detail', $fsID);
        $pagination->setBaseURL((string) $detailURL->setQuery(['itemsPerPage' => $itemsPerPage]));
        $this->set('files', $pagination->getCurrentPageResults());
        $this->set('pagination', $pagination);
        $this->set('itemsPerPage', $itemsPerPage);
        $this->set('itemsPerPageOptions', $options);
        $this->set('fileSetDetailURL', $detailURL);
        $this->set('paginationQuery', array_intersect_key($this->request->query->all(), array_flip(['itemsPerPage', 'ccm_paging_fl', 'ccm_cursor'])));
    }

    public function file_sets_edit()
    {
        extract($this->getHelperObjects());

        $valt = Loader::helper('validation/token');
        if (!$valt->validate("file_sets_edit")) {
            $this->error->add($valt->getErrorMessage());
        }

        if (!$this->post('fsID')) {
            $this->error->add(t('Invalid ID'));
        }
        $setName = trim($this->post('file_set_name'));
        if (!$setName) {
            $this->error->add(t('Please Enter a Name'));
        }

        if (!$this->error->has()) {
            $file_set = FileSet::getByID($this->post('fsID'));


            $file_set->update($setName);
            $file_set->updateFileSetDisplayOrder($this->getFileSetDisplayOrder($file_set));

            return $this->getFileSetRedirect($file_set, 'file_set_updated');
        } else {
            $this->view();
        }
    }

    public function remove_files()
    {
        $token = $this->app->make('token');
        if (!$this->request->isMethod('POST') || !$token->validate('remove_file_set_files')) {
            throw new UserMessageException($token->getErrorMessage());
        }

        $setID = $this->request->request->all()['fsID'] ?? null;
        $fs = is_scalar($setID) && ctype_digit((string) $setID) && (int) $setID > 0 ? FileSet::getByID($setID) : false;
        $user = $this->app->make(User::class);
        if (!$fs || ($fs->getFileSetType() != FileSet::TYPE_PUBLIC && $fs->getFileSetUserID() != $user->getUserID())) {
            $this->flash('error', t('Invalid file set.'));

            return $this->buildRedirect($this->getPageObject());
        }

        try {
            $files = $this->getFilesToRemove($fs);
            foreach ($files as $file) {
                $fs->removeFileFromSet($file);
                $version = $file->getApprovedVersion();
                if ($version) {
                    $version->releaseImagineImage();
                }
            }
            $this->flash('success', t2('%s file removed from the set.', '%s files removed from the set.', count($files)));
        } catch (UserMessageException $e) {
            $this->flash('error', $e->getMessage());
        }

        return $this->getFileSetRedirect($fs);
    }

    private function getFileSetRedirect(FileSet $fs, $action = null)
    {
        $query = array_intersect_key($this->request->query->all(), array_flip(['itemsPerPage', 'ccm_paging_fl', 'ccm_cursor']));
        $url = Url::to($this->getPageObject(), 'view_detail', $fs->getFileSetID(), $action)->setQuery($query);

        return $this->buildRedirect((string) $url);
    }

    private function getFileSetDisplayOrder(FileSet $fs)
    {
        $rows = $this->app->make('database')->connection()->fetchAll(
            'SELECT fID FROM FileSetFiles WHERE fsID = ? ORDER BY fsDisplayOrder, fID',
            [$fs->getFileSetID()]
        );
        $fileIDs = array_column($rows, 'fID');
        $existing = array_flip($fileIDs);
        $submitted = $this->request->request->all()['fsDisplayOrder'] ?? [];
        $selected = [];
        foreach (is_array($submitted) ? $submitted : [] as $fileID) {
            if (is_scalar($fileID) && ctype_digit((string) $fileID) && isset($existing[(int) $fileID])) {
                $selected[(int) $fileID] = (int) $fileID;
            }
        }
        $ordered = array_values($selected);
        $index = 0;
        foreach ($fileIDs as &$fileID) {
            if (isset($selected[$fileID])) {
                $fileID = $ordered[$index++];
            }
        }
        unset($fileID);

        return $fileIDs;
    }

    private function getFilesToRemove(FileSet $fs)
    {
        $fileIDs = $this->request->request->all()['fID'] ?? null;
        $files = [];
        $permissions = FilePermissions::getGlobal();
        if (!is_array($fileIDs) || !$fileIDs) {
            throw new UserMessageException(t('Please select at least one file.'));
        }

        foreach ($fileIDs as $fileID) {
            $file = is_scalar($fileID) && ctype_digit((string) $fileID) ? File::getByID($fileID) : null;
            $filePermissions = $file ? new Permissions($file) : null;
            if (!$file || !$file->inFileSet($fs) || !$filePermissions->canViewFileInFileManager() || !$permissions->canAddFile($file)) {
                throw new UserMessageException(t('Unable to remove one or more files from this set.'));
            }
            $files[$file->getFileID()] = $file;
        }

        return $files;
    }
}
