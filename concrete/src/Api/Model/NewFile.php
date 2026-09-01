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
     * @OA\Property(type="string", format="binary", title="File to upload", description="The contents of the file, sent as the ""file"" part of the multipart/form-data request body: that's what a <input type=""file"" name=""file""> field of an HTML form sends. Use the url property instead if you can't send a multipart request.")
     *
     * @var string
     */
    private $file;

    /**
     * @OA\Property(type="string", format="uri", title="URL of the file to be downloaded", description="Let Concrete download the file itself, instead of sending its contents. The name of the file is taken from the URL (or from the mime type declared by the remote server), and its extension must be one that this installation accepts.")
     *
     * @var string
     */
    private $url;

}
