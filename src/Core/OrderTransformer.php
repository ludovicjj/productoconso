<?php

namespace App\Core;

use App\Search\OrderSearch;

/**
 * Class OrderTransformer
 * @package App\Core
 */
class OrderTransformer
{
    /**
     * Transform query params "orders" to an array of OrderSearch.
     * If query param "order" is null, return an empty array.
     *
     * @param string|null $queryOrder
     * @return array|OrderSearch[]
     */
    public static function transformQueryToArray(?string $queryOrder): array
    {
        if (null === $queryOrder) {
            return [];
        }

        $orders = explode(',', $queryOrder);
        $resultOrder = [];

        foreach ($orders as $order) {
            $direction = 'asc';
            if (strpos($order, "-") === 0) {
                $direction = 'desc';
                $order = substr($order, 1);
            }
            $resultOrder[] = new OrderSearch($order, $direction);
        }

        return $resultOrder;
    }
}
