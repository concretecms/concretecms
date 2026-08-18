<?php

namespace Concrete\Core\Api\Model;

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
     * @OA\Property(type="string", format="uri", title="URL of the file to be downloaded", description="Let Concrete download the file itself. The name of the file is taken from the URL (or from the mime type declared by the remote server), and its extension must be one that this installation accepts.")
     *
     * @var string
     */
    private $url;

    /**
     * @OA\Property(type="string", title="Name of the file", description="Required when sending the contents: the name the file will have, for example image.png. Its extension must be one that this installation accepts.")
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
