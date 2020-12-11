<?php

namespace App\Search;

/**
 * Class OrderSearch
 * @package App\Search
 */
class OrderSearch
{
    /** @var string $order */
    private $order;

    /** @var string $direction */
    private $direction;

    public function __construct(
        string $order,
        string $direction
    ) {
        $this->order = $order;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }
}
