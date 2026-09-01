<?php

namespace Concrete\Core\Command\Task\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\Definition\BooleanField;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\Field;
use Concrete\Core\Command\Task\Input\Definition\SelectField;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Command\GeneratedThumbnailCommand;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Image\Thumbnail\Type\Type;

class GenerateThumbnailsController extends AbstractController
{
    public function getName(): string
    {
        return t('Generate thumbnails');
    }

    public function getDescription(): string
    {
        return t('Recomputes thumbnails for files. Optionally select a thumbnail type and/or filter to file IDs. Generates 2x retina versions where applicable.');
    }

    public function getInputDefinition(): ?Definition
    {
        $definition = new Definition();
        
        // Get all available thumbnail types (exclude 2x versions since they're auto-generated)
        $thumbnailTypes = Type::getVersionList();
        $options = ['all' => t('All Thumbnail Types')];
        
        foreach ($thumbnailTypes as $type) {
            $handle = $type->getHandle();
            // Only include base types (not the _2x variants)
            if (substr($handle, -3) !== '_2x') {
                $options[$handle] = $type->getName() . ' (' . $handle . ')';
            }
        }
        
        $definition->addField(
            new SelectField(
                'thumbnail_type',
                t('Thumbnail Type'),
                t('Select a specific thumbnail type to generate (includes both standard and 2x retina versions), or choose "All" to regenerate all types.'),
                $options,
                true // required - defaults to "All"
            )
        );
        
        $definition->addField(
            new Field(
                'file_ids',
                t('File IDs (Optional)'),
                t('Enter a single file ID or comma-separated list of file IDs to process only specific files. Leave blank to process all files.'),
                false // not required
            )
        );
        
        $definition->addField(
            new BooleanField(
                'force_regenerate',
                t('Force Regenerate'),
                t('Delete existing thumbnails before regenerating. Enable this if you changed thumbnail settings (like upscaling or cropping) and need to recreate existing thumbnails.')
            )
        );
        
        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $fileList = new FileList();
        
        // Get selected thumbnail type from input
        $selectedType = 'all';
        if ($input->hasField('thumbnail_type')) {
            $field = $input->getField('thumbnail_type');
            $selectedType = $field->getValue();
        }
        
        // Get file IDs filter from input
        $fileIds = [];
        if ($input->hasField('file_ids')) {
            $field = $input->getField('file_ids');
            $fileIdsValue = trim($field->getValue());
            if (!empty($fileIdsValue)) {
                // Parse comma-separated list
                $fileIds = array_map('trim', explode(',', $fileIdsValue));
                $fileIds = array_filter($fileIds, 'is_numeric');
                $fileIds = array_map('intval', $fileIds);
            }
        }
        
        // Get force regenerate option
        $forceRegenerate = false;
        if ($input->hasField('force_regenerate')) {
            $field = $input->getField('force_regenerate');
            $forceRegenerate = (bool) $field->getValue();
        }
        
        // Get thumbnail types to process
        if ($selectedType === 'all') {
            $thumbnailTypeVersions = Type::getVersionList();
            // Convert Version objects to Type objects
            $thumbnailTypes = [];
            $processedHandles = [];
            foreach ($thumbnailTypeVersions as $version) {
                $handle = $version->getHandle();
                // Skip _2x variants and already processed handles
                if (substr($handle, -3) === '_2x' || in_array($handle, $processedHandles)) {
                    continue;
                }
                $type = Type::getByHandle($handle);
                if ($type) {
                    $thumbnailTypes[] = $type;
                    $processedHandles[] = $handle;
                }
            }
        } else {
            $thumbnailType = Type::getByHandle($selectedType);
            $thumbnailTypes = $thumbnailType ? [$thumbnailType] : [];
        }

        $batch = Batch::create();
        $totalFiles = 0;
        $totalThumbnails = 0;

        foreach ($fileList->getResults() as $file) {
            if ($file instanceof File) {
                // If file IDs filter is set, skip files not in the list
                if (!empty($fileIds) && !in_array($file->getFileID(), $fileIds)) {
                    continue;
                }
                
                $totalFiles++;
                foreach ($file->getFileVersions() as $fileVersion) {
                    foreach ($thumbnailTypes as $thumbnailType) {
                        if ($fileVersion->getTypeObject()->supportsThumbnails()) {
                            // If force regenerate is enabled, delete existing thumbnails first
                            if ($forceRegenerate) {
                                // Delete both base and doubled versions
                                $baseVersion = $thumbnailType->getBaseVersion();
                                $doubledVersion = $thumbnailType->getDoubledVersion();
                                
                                if ($baseVersion) {
                                    $fileVersion->deleteThumbnail($baseVersion);
                                }
                                if ($doubledVersion) {
                                    $fileVersion->deleteThumbnail($doubledVersion);
                                }
                            }
                            
                            $batch->add(new GeneratedThumbnailCommand(
                                (int)$file->getFileID(),
                                (int)$fileVersion->getFileVersionID(),
                                $thumbnailType->getHandle()
                            ));
                            $totalThumbnails++;
                        }
                    }
                }
            }
        }

        // Create a more informative message
        $fileFilterMsg = !empty($fileIds) ? t(' (filtered to %s specific files)', count($fileIds)) : '';
        
        if ($selectedType === 'all') {
            $message = t('Generating all thumbnail types for %s files%s (%s total thumbnails)...', 
                $totalFiles,
                $fileFilterMsg,
                $totalThumbnails
            );
        } else {
            $typeName = '';
            foreach ($thumbnailTypes as $type) {
                $typeName = $type->getName();
                break;
            }
            $message = t('Generating "%s" thumbnails for %s files%s (%s total thumbnails)...', 
                $typeName,
                $totalFiles,
                $fileFilterMsg,
                $totalThumbnails
            );
        }

        return new BatchProcessTaskRunner($task, $batch, $input, $message);
    }
}
