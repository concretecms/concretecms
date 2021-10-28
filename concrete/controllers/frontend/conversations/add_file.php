<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\User\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class AddFile extends FrontendController
{
    public function view(): Response
    {
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $post = $this->request->request;
        try {
            $this->checkToken();
            $conversation = $this->getBlockConversation();
            $this->checkConversation($conversation);
            $file = $this->getPostedFile();
            $this->checkPostedFileLimits($conversation, $file);
            $this->checkPostedFileExtension($conversation, $file);
            $fileVersion = $this->importFile($conversation, $file);

            return $responseFactory->json([
                'id' => (int) $fileVersion->getFileID(),
                'timestamp' => $post->get('timestamp'),
                'tag' => $post->get('tag'),
            ]);
        } catch (UserMessageException $x) {
            return $responseFactory->json([
                'error' => $x->getMessage(),
                'timestamp' => $post->get('timestamp'),
            ]);
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkToken(): void
    {
        $val = $this->app->make('token');
        if (!$val->validate('add_conversations_file')) {
            throw new UserMessageException($val->getErrorMessage());
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkConversation(Conversation $conversation): void
    {
        if ($conversation->getConversationAttachmentOverridesEnabled() > 0) {
            // Check individual conversation for allowing attachments.
            if (!$conversation->getConversationAttachmentsEnabled()) {
                throw new UserMessageException(t('This conversation does not allow file attachments.'));
            }
        } else {
            // Check global config settings for whether or not file attachments should be allowed.
            $config = $this->app->make('config');
            if (!$config->get('conversations.attachments_enabled')) {
                throw new UserMessageException(t('This conversation does not allow file attachments.'));
            }
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getPostedFile(): UploadedFile
    {
        $file = $this->request->files->get('file');
        if (!($file instanceof UploadedFile)) {
            throw new UserMessageException(t('File not received'));
        }
        if (!$file->isValid()) {
            throw new UserMessageException(ImportException::describeErrorCode($file->getError()));
        }

        return $file;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkPostedFileLimits(Conversation $conversation, UploadedFile $file): void
    {
        list($maxFileSize, $maxQuantity) = $this->getAttachmentLimits($conversation);
        if ($maxFileSize !== null && $file->getSize() > $maxFileSize * 1000000) {
            throw new UserMessageException(t('File size exceeds limit'));
        }
        // check file count (this is just for presentation, final count check is done on message submit).
        if ($maxQuantity !== null && (int) $this->request->request->get('fileCount') > $maxQuantity) {
            throw new UserMessageException(tt('Attachment limit reached'));
        }
    }

    /**
     * @return array First element: max file size (int; NULL: unlimited), second element: max quantity (int, or NULL if unlimited)
     */
    protected function getAttachmentLimits(Conversation $conversation): array
    {
        $u = $this->app->make(User::class);
        $config = $this->app->make('config');
        if ($u->isRegistered()) {
            if ($conversation->getConversationAttachmentOverridesEnabled()) {
                $maxFileSize = $conversation->getConversationMaxFileSizeRegistered();
                $maxQuantity = $conversation->getConversationMaxFilesRegistered();
            } else {
                $maxFileSize = $config->get('conversations.files.registered.max_size');
                $maxQuantity = $config->get('conversations.files.registered.max');
            }
        } else {
            if ($conversation->getConversationAttachmentOverridesEnabled()) {
                $maxFileSize = $conversation->getConversationMaxFilesGuest();
                $maxQuantity = $conversation->getConversationMaxFilesGuest();
            } else {
                $maxFileSize = $config->get('conversations.files.guest.max_size');
                $maxQuantity = $config->get('conversations.files.guest.max');
            }
        }

        return [
            (int) $maxFileSize <= 0 ? null : (int) $maxFileSize,
            (string) $maxQuantity === '' ? null : (int) $maxQuantity,
        ];
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkPostedFileExtension(Conversation $conversation, UploadedFile $file): void
    {
        $incomingExtension = mb_strtolower($file->getClientOriginalExtension());
        if ($incomingExtension === '') {
            return;
        }
        $extensionIsValid = false;
        $denylistedExtensions = $this->getDenylistedFileExtensions($conversation);
        if (!in_array($incomingExtension, $denylistedExtensions, true)) {
            $allowedExtensions = $this->getAllowedFileExtensions($conversation);
            $extensionIsValid = $allowedExtensions === [] || in_array($incomingExtension, $allowedExtensions, true);
        }
        if ($extensionIsValid === false) {
            throw new UserMessageException(t('Invalid File Extension'));
        }
    }

    /**
     * Get the list of denylisted file extensions (lower case).
     *
     * @return string[]
     */
    protected function getDenylistedFileExtensions(Conversation $conversation): array
    {
        $config = $this->app->make('config');
        $extensions = $config->get('conversations.files.disallowed_types');
        if ($extensions === null) {
            $extensions = $config->get('concrete.upload.extensions_denylist', $config->get('concrete.upload.extensions_blacklist'));
        }
        $helperFile = $this->app->make('helper/concrete/file');

        return array_map('mb_strtolower', $helperFile->unserializeUploadFileExtensions($extensions));
    }

    /**
     * Get the list of allowed file extensions (lower case).
     *
     * @return string[]
     */
    protected function getAllowedFileExtensions(Conversation $conversation): array
    {
        if ($conversation->getConversationAttachmentOverridesEnabled()) {
            $extensions = $conversation->getConversationFileExtensions();
        } else {
            $config = $this->app->make('config');
            $extensions = $config->get('conversations.files.allowed_types');
        }
        $helperFile = $this->app->make('helper/concrete/file');

        return array_map('mb_strtolower', $helperFile->unserializeUploadFileExtensions($extensions));
    }

    protected function importFile(Conversation $conversation, UploadedFile $file): Version
    {
        $fileImporter = $this->app->make(FileImporter::class);
        $fileVersion = $fileImporter->importUploadedFile($file);
        $this->assignImportedFileToFileSet($conversation, $fileVersion);

        return $fileVersion;
    }

    protected function assignImportedFileToFileSet(Conversation $conversation, Version $fileVersion): ?FileSet
    {
        $fileSet = $this->getFileSetForUploadedFile($conversation);
        if ($fileSet !== null) {
            $fileSet->addFileToSet($fileVersion);
        }

        return $fileSet;
    }

    protected function getFileSetForUploadedFile(Conversation $conversation): ?FileSet
    {
        $config = $this->app->make('config');
        $fileSetName = $config->get('conversations.attachments_pending_file_set');
        $fileSet = FileSet::getByName($fileSetName);
        if ($fileSet === null) {
            $fileSet = FileSet::createAndGetSet($fileSetName, FileSet::TYPE_PUBLIC, USER_SUPER_ID);
        }

        return $fileSet;
    }
}
