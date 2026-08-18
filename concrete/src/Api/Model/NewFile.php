<?php

namespace Concrete\Core\Api\Model;

/**
 * @OA\Schema(title="NewFile model", description="Either file or url must be specified")
 */
class NewFile
{

    /**
     * @OA\Property(type="int", title="Folder ID")
     *
     * @var string
     */
    private $folder;

    /**
     * @OA\Property(type="string", format="binary", title="File to upload")
     *
     * @var string
     */
    private $file;

    /**
     * @OA\Property(type="string", format="uri", title="URL of the file to be downloaded", description="Let Concrete download the file itself, instead of uploading its contents. The name of the file is taken from the URL (or from the mime type declared by the remote server), and its extension must be one that this installation accepts.")
     *
     * @var string
     */
    private $url;

}
