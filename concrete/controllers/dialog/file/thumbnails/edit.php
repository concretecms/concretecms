<?php
namespace Concrete\Controller\Dialog\File\Thumbnails;

use Concrete\Controller\Backend\File\Importer\Thumbnail;
use Concrete\Controller\Backend\UserInterface\File as BackendInterfaceFileController;
use Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\File\Type\Type;
use Concrete\Core\ImageEditor\ImageEditorService;
use Concrete\Core\Permission\Checker;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Edit extends BackendInterfaceFileController
{
    protected $viewPath = '/dialogs/file/thumbnails/edit';

    protected $validationToken = 'update_thumbnail';

    protected function canAccess()
    {
        $type = $this->file->getTypeObject();
        return $this->permissions->canEditFileContents() && $type->getGenericType() == Type::T_IMAGE;
    }

    protected function getThumbnailTypeVersionFromRequest(): Version
    {
        $handle = $this->request->query->get('thumbnail');
        $thumbnails = $this->file->getThumbnails();
        foreach ($thumbnails as $thumb) {
            $tempVersion = $thumb->getThumbnailTypeVersionObject();
            if ($tempVersion->getHandle() === $handle) {
                $typeVersion = $tempVersion;
                break;
            }
        }
        return $typeVersion;
    }

    public function view()
    {
        $typeVersion = $this->getThumbnailTypeVersionFromRequest();
        $editorService = $this->app->make(ImageEditorService::class);
        $this->set('thumbnailTypeVersion', $typeVersion);
        $this->set('fv', $this->file->getVersionToModify());
        $this->set('editorService', $editorService);
    }

    public function submit()
    {
        if ($this->validateAction()) {
            $fp = new Checker($this->file);
            if ($fp->canEditFileProperties()) {
                $fileVersion = $this->file->getVersionToModify();
                $uploadedFile = $this->request->files->get('file');
                $typeVersion = $this->getThumbnailTypeVersionFromRequest();
                $fsl = $this->file->getFileStorageLocationObject();
                $filesystem = $fsl->getFileSystemObject();
                /**
                 * @var $uploadedFile UploadedFile
                 */
                $filesystem->update($typeVersion->getFilePath($fileVersion), $uploadedFile->getContent());

                $sr = new FileEditResponse();
                $sr->setFile($this->file);
                $sr->setMessage(t('File updated successfully.'));
                $sr->outputJSON();
            } else {
                throw new Exception(t('Access Denied.'));
            }
        } else {
            throw new Exception(t('Access Denied.'));
        }
    }
}
