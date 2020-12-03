<?php

namespace App\Repository;

use App\Entity\Farm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }

    /**
     * @param string $slug
     * @return string
     */
    public function makeUniqueSlug(string $slug): string
    {
        $slugsFound = $this->createQueryBuilder("f")
            ->select("f.slug")
            ->where("REGEXP(f.slug, :pattern) > 0")
            ->setParameter("pattern", $slug)
            ->getQuery()
            ->getScalarResult();

        if (count($slugsFound) === 0) {
            return $slug;
        }

        $result = array_map(function (string $slugFound) use ($slug) {
            preg_match("/^" . $slug . "-([0-9]*)$/", $slugFound, $matches);
            return !isset($matches[1]) ? 0 : intval($matches[1]);
        }, array_column($slugsFound, 'slug'));

        rsort($result);

        return sprintf("%s-%d", $slug, $result[0] + 1);
    }
}
