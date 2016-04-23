<?php
namespace Concrete\Controller\Backend\File;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\FolderItemList;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\Search\Result\Result;
use Symfony\Component\HttpFoundation\JsonResponse;

class Folder extends AbstractController
{

    public function add()
    {
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        $folder = $filesystem->addFolder($folder, $this->request->request->get('folderName'));
        $response = new EditResponse();
        $response->setMessage(t('Folder added.'));
        $response->setAdditionalDataAttribute('folder', $folder);
        $response->outputJSON();
    }


}
