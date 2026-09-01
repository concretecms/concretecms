<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Model;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @OA\Schema(
 *     title="NewFileFromJson model",
 *     description="Adding a file without sending a multipart/form-data request body: specify either url, or both filename and contents"
 * )
 */
class NewFileFromJson
{
    /**
     * @OA\Property(type="int", title="Folder ID")
     *
     * @var string
     */
    private $folder;

    /**
     * @OA\Property(type="string", format="uri", title="URL of the file to be downloaded", description="Let Concrete download the file itself. Unless filename is specified, the name of the file is taken from the URL (or from the mime type declared by the remote server); in both the cases the extension must be one that this installation accepts.")
     *
     * @var string
     */
    private $url;

    /**
     * @OA\Property(type="string", title="Name of the file", description="The name the file will have, for example image.png: its extension must be one that this installation accepts. It's required when sending the contents, and optional when downloading from an url (where it overrides the name taken from the URL).")
     *
     * @var string
     */
    private $filename;

    /**
     * @OA\Property(type="string", format="byte", title="Contents of the file", description="The contents of the file, base64-encoded, to be sent together with filename. Use a multipart/form-data request body if you can: it doesn't need the contents to be encoded.")
     *
     * @var string
     */
    private $contents;
}
