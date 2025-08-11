<?php
namespace Concrete\Core\Health\Grade;

abstract class AbstractGrade implements GradeInterface
{

    /**
     * @var int
     */
    protected $score;

    public function __construct(int $score)
    {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }


    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => static::class,
            'score' => $this->score,
        ];
    }


}
