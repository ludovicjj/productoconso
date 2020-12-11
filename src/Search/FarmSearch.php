<?php

namespace App\Search;

/**
 * Class FarmSearch
 * @package App\Search
 */
class FarmSearch
{
    /** @var array|OrderSearch[] $orders */
    private $orders;

    public const ORDER_BY_NAME = 'name';
    public const ORDER_BY_SLUG = 'slug';

    public const ALLOWED_ORDERS = [
        self::ORDER_BY_NAME,
        self::ORDER_BY_SLUG
    ];

    /**
     * FarmSearch constructor.
     * @param array|OrderSearch[] $orders
     */
    public function __construct(
        array $orders
    ) {
        $this->orders = $this->valideOrders($orders);
    }

    /**
     * @return array|OrderSearch[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * If orders is an empty, return array.
     * Else check foreach OrderSearch contain an allowed order
     *
     * @param array|OrderSearch[] $orders
     * @return array
     */
    private function valideOrders(array $orders): array
    {
        if (empty($orders)) {
            return [];
        }

        return array_filter($orders, function (OrderSearch $order) {
            if (!in_array($order->getOrder(), self::ALLOWED_ORDERS)) {
                return false;
            } else {
                return true;
            }
        });
    }
}
