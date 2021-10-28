<?php

namespace Concrete\Core\Entity\File\Folder;

use Concrete\Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UserFavoriteFolders"
 * )
 */
class FavoriteFolder
{
    /**
     * The owner of the favorite folder.
     *
     * @var User
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
     */
    protected $owner = null;

    /**
     * The tree node id of the favorite folder.
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $treeNodeFolderId;

    /**
     * Get the owner of the favorite folder.
     *
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * Set the owner of the favorite folder.
     *
     * @param User $owner
     * @return FavoriteFolder
     */
    public function setOwner(User $owner): FavoriteFolder
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get the tree node id of the favorite folder.
     *
     * @return int
     */
    public function getTreeNodeFolderId(): int
    {
        return $this->treeNodeFolderId;
    }

    /**
     * Set the tree node id of the favorite folder.
     *
     * @param int $treeNodeFolderId
     * @return FavoriteFolder
     */
    public function setTreeNodeFolderId(int $treeNodeFolderId): FavoriteFolder
    {
        $this->treeNodeFolderId = $treeNodeFolderId;
        return $this;
    }

}