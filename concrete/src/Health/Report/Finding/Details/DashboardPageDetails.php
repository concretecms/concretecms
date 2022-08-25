<?php
namespace Concrete\Core\Health\Report\Finding\Details;


class DashboardPageDetails implements DetailsInterface
{

    /**
     * @var string
     */
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'path' => $this->path,
        ];

    }

}
