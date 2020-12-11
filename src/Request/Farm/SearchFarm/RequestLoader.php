<?php

namespace App\Request\Farm\SearchFarm;

use App\Core\OrderTransformer;
use App\Entity\Farm;
use App\Repository\FarmRepository;
use App\Search\FarmSearch;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestLoader
 * @package App\Request\Farm
 */
class RequestLoader
{
    /** @var FarmRepository $farmRepository */
    private $farmRepository;

    public function __construct(
        FarmRepository $farmRepository
    ) {
        $this->farmRepository = $farmRepository;
    }

    /**
     * Load farm(s) from database.
     *
     * @param Request $request
     * @return Farm[]|array
     */
    public function load(Request $request)
    {
        return $this->loadFromDatabase($request);
    }

    /**
     * Use data from FarmSearch to load farm from database.
     *
     * @param Request $request
     * @return Farm[]|array
     */
    private function loadFromDatabase(Request $request)
    {
        return $this->farmRepository->search(
            new FarmSearch(
                OrderTransformer::transformQueryToArray($request->query->get('order'))
            )
        );
    }
}
