<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ThumbnailMiddleware
 * Middleware used to populate thumbnails at the end of each request
 *
 * This middleware requires the following to be defined on the Application:
 * "database" DatabaseManager
 * "BasicThumbnailer::class" Basic thumbnailer
 *
 * @package Concrete\Core\Http\Middleware
 */
class ThumbnailMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        if ($response->getStatusCode() == 200) {
            /** @var Connection $database */
            try {
                $database = $this->app->make('database')->connection();
            } catch (\InvalidArgumentException $e) {
                // Don't die here if there's no available database connection
            }

            if ($database) {
                try {
                    $paths = $database->fetchAll('SELECT * FROM FileImageThumbnailPaths WHERE isBuilt=0 LIMIT 5');
                    $this->generateThumbnails($paths, $database);
                } catch (InvalidFieldNameException $e) {
                    // Ignore this, user probably needs to run an upgrade.
                }
            }
        }

        return $response;
    }

    private function generateThumbnails($paths, Connection $database)
    {
        $database->transactional(function(Connection $database) use ($paths) {
            foreach ($paths as $thumbnail) {
                /** @var EntityManagerInterface $orm */
                $orm = $this->app->make('database/orm')->entityManager();
                /** @var File $file */
                $file = $orm->find(File::class, $thumbnail['fileID']);

                if ($dimensions = $this->getDimensions($thumbnail)) {
                    list($width, $height, $crop) = $dimensions;

                    /** @var BasicThumbnailer $thumbnailer */
                    $thumbnailer = $this->app->make(BasicThumbnailer::class);
                    $thumbnailer->getThumbnail($file, $width, $height, !!$crop);

                    $database->query(
                        'UPDATE FileImageThumbnailPaths set isBuilt=1 where fileID=? AND fileVersionID=? AND thumbnailTypeHandle=? AND path=?',
                        [
                            $thumbnail['fileID'],
                            $thumbnail['fileVersionID'],
                            $thumbnail['thumbnailTypeHandle'],
                            $thumbnail['path']
                        ])->execute();
                }
            }
        });
    }

    private function getDimensions($thumbnail)
    {
        $matches = null;

        if (preg_match('/ccm_(\d+)x(\d+)(?:_([10]))?/', $thumbnail['thumbnailTypeHandle'], $matches)) {
            return array_pad(array_slice($matches, 1), 3, 0);
        }
    }

}
