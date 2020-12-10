<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ParameterBagTransformer
 * @package App\Core
 */
class ParameterBagTransformer
{
    /**
     * @param ParameterBag $parameterBag
     * @return array
     */
    public function transformQueryToContext(ParameterBag $parameterBag): array
    {
        $query = [
            'fields' => $this->getFields($parameterBag),
            'includes' => $this->getIncludes($parameterBag)
        ];

        return ['query' => $query];
    }

    /**
     * @param ParameterBag $parameterBag
     * @return array
     */
    private function getFields(ParameterBag $parameterBag): array
    {
        if (!$parameterBag->has('fields')) {
            return [];
        }

        return array_map(function ($fields) {
            return explode(',', $fields);
        }, $parameterBag->all('fields'));
    }

    /**
     * @param ParameterBag $parameterBag
     * @return array
     */
    private function getIncludes(ParameterBag $parameterBag): array
    {
        if (!$parameterBag->has('includes')) {
            return [];
        }

        return explode(',', $parameterBag->get('includes'));
    }
}
