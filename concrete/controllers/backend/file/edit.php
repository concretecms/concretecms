<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Importer;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Edit extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/backend/file/edit';

    public function view(): ?Response
    {
        $file = $this->getFile();
        $this->checkAccessibleFile($file);
        $this->set('file', $file);
        $this->set('fileVersion', $file->getApprovedVersion());
        $this->set('app', $this->app);

        return null;
    }

    public function save($fID = null)
    {
        /** @var Token $tokenValidator */
        $tokenValidator = $this->app->make(Token::class);
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        /** @var ErrorList $errorList */
        $errorList = $this->app->make(ErrorList::class);
        /** @var \Concrete\Core\File\Service\File $fileHelper */
        $fileHelper = $this->app->make(\Concrete\Core\File\Service\File::class);
        $editResponse = new EditResponse();
        $fileImporter = new Importer();

        $token = $this->request->request->get("token");

        if ($tokenValidator->validate("", $token)) {
            $file = \Concrete\Core\File\File::getByID($fID);

            if ($file instanceof File) {
                $permissionChecker = new Checker($file);
                $responseObject = $permissionChecker->getResponseObject();

                if ($responseObject->validate("edit_file_contents")) {
                    $approvedVersion = $file->getApprovedVersion();

                    if ($approvedVersion instanceof Version) {
                        $imageDataBase64 = $this->request->request->get("imageData");

                        if (strtolower(substr($imageDataBase64, 0, 5)) === "data:") {

                            $tmpName = tempnam($fileHelper->getTemporaryDirectory(), 'img');
                            $imageData = base64_decode(preg_replace('/data:image\/(png|jpeg);base64,/', '', $imageDataBase64, 1));
                            $fileHelper->append($tmpName, $imageData);

                            $newVersion = $fileImporter->import($tmpName, $approvedVersion->getFileName(), $file);

                            if (!$newVersion instanceof Version) {
                                $errorList->add(t("Error while saving the image data."));
                            }
                        } else {
                            $errorList->add(t("Image data is missing or invalid."));
                        }

                    } else {
                        $errorList->add(t("The given file has no approved version."));
                    }
                } else {
                    $errorList->add(t("You don't have the permission to edit this file."));
                }
            } else {
                $errorList->add(t("The given file id is invalid."));
            }
        } else {
            $errorList->add($tokenValidator->getErrorMessage());
        }

        $editResponse->setError($errorList);
        $editResponse->setTitle(t("Image saved."));
        $editResponse->setMessage(t("The image has been successfully saved."));

        return $responseFactory->json($editResponse);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFile(): File
    {
        $fileID = $this->request->request->get('fID', $this->request->query->get('fID'));
        $fileID = $this->app->make(Numbers::class)->integer($fileID, 1) ? (int)$fileID : null;
        $file = $fileID === null ? null : $this->app->make(EntityManagerInterface::class)->find(File::class, $fileID);
        if ($file === null) {
            throw new UserMessageException(t('Unable to find the specified file.'));
        }

        return $file;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkAccessibleFile(File $file): void
    {
        $fp = new Checker($file);
        if (!$fp->canEditFileContents()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }
}
