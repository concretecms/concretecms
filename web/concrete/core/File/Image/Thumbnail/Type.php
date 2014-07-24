<?php
namespace Concrete\Core\File\Image\Thumbnail;

use Database;

/**
 * @Entity
 * @Table(name="FileImageThumbnailTypes")
 */
class Type
{

    /**
     * @Column(type="string")
     */
    protected $ftTypeHandle;

    /**
     * @Column(type="string")
     */
    protected $ftTypeName;

    /**
     * @Column(type="integer")
     */
    protected $ftTypeWidth = 0;

    /**
     * @Column(type="boolean")
     */
    protected $ftTypeIsRequired = false;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $ftTypeID;

    /**
     * @param mixed $ftTypeHandle
     */
    public function setHandle($ftTypeHandle)
    {
        $this->ftTypeHandle = $ftTypeHandle;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->ftTypeHandle;
    }

    /**
     * @param mixed $ftTypeIsRequired
     */
    public function requireType()
    {
        $this->ftTypeIsRequired = true;
    }

    /**
     * @return mixed
     */
    public function isRequired()
    {
        return $this->ftTypeIsRequired;
    }

    /**
     * @param mixed $ftTypeName
     */
    public function setName($ftTypeName)
    {
        $this->ftTypeName = $ftTypeName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->ftTypeName;
    }

    /**
     * @param mixed $ftTypeWidth
     */
    public function setWidth($ftTypeWidth)
    {
        $this->ftTypeWidth = $ftTypeWidth;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->ftTypeWidth;
    }



    public static function getList()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\File\Image\Thumbnail\Type')->findBy(array(), array('ftTypeWidth' => 'asc'));
    }

    public function save()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function exportList($node)
    {
        $child = $node->addChild('thumbnailtypes');
        $list = static::getList();
        foreach($list as $link) {
            $linkNode = $child->addChild('thumbnailtype');
//            $linkNode->addAttribute('service', $link->getServiceObject()->getHandle());
  //          $linkNode->addAttribute('url', $link->getURL());

        }
    }

    public function delete()
    {
        $em = Database::get()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }

    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\File\Image\Thumbnail\Type', $id);
        return $r;
    }
}