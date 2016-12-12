<?php
namespace Concrete\Core\Activity;

use Concrete\Core\Http\Service\Json;

/**
 * Class NewsflowItem.
 *
 * \@package Concrete\Core\Activity
 */
class NewsflowItem
{
    protected $id;
    protected $title;
    protected $content;
    protected $date;
    protected $description;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * todo: determine return type.
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $content
     * @param mixed $date todo: needs type fixed
     * @param string $description
     */
    public function __construct($id = null, $title = null, $content = null, $date = null, $description = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
        $this->description = $description;
    }

    /**
     * @param string $response A JSON encoded array. Expected format of:
     * <code>
     * {
     *  'id': value,
     *  'title': value,
     *  'content': value,
     *  'date': value,
     *  'description': value
     * }
     * </code>
     *
     * @return NewsflowItem Returns a new NewsflowItem if one could be created from the response, otherwise throws an exception
     *
     * @throws \Exception
     */
    public function parseResponse($response)
    {
        try {
            $json = new Json();
            $obj = $json->decode($response);
            if (is_object($obj) && isset($obj->id) && isset($obj->title) && isset($obj->content)
                && isset($obj->date) && isset($obj->description)
            ) {
                return new self(
                    $obj->id,
                    $obj->title,
                    $obj->content,
                    $obj->date,
                    $obj->description
                );
            } else {
                throw new \Exception(t('Unable to parse news response.'));
            }
        } catch (\Exception $e) {
            throw new \Exception(t('Unable to parse news response.'));
        }
    }
}
