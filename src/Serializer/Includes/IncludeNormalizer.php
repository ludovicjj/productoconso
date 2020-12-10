<?php

namespace App\Serializer\Includes;

use ArrayObject;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class IncludeNormalizer
 * @package App\Serializer\Includes
 */
class IncludeNormalizer
{
    /** @var PropertyAccessor $propertyAccessor */
    private $propertyAccessor;

    /** @var NormalizerInterface $normalizer */
    private $normalizer;

    public function __construct(
        NormalizerInterface $normalizer
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->normalizer = $normalizer;
    }

    /**
     * @param $object
     * @param $format
     * @param array $context
     * @param array $allowedIncludes
     * @return array|array[]|ArrayObject[]|bool[]|float[]|int[]|null[]|string[]
     * @throws ExceptionInterface
     */
    public function normalizeIncludes(
        $object,
        $format,
        array $context = [],
        array $allowedIncludes = []
    ) {
        $context['query']['includes'] = $this->filterIncludes($context, $allowedIncludes);
        return $this->getIncludes($object, $format, $context);
    }

    /**
     * Check if included fields begin by allowedIncludes (in my case "adresse")
     * adresse.city, adresse.zipCode, etc...
     *
     * @param array $context
     * @param array $allowedIncludes
     * @return array
     */
    private function filterIncludes(array $context, array $allowedIncludes): array
    {
        return array_filter(array_unique($context['query']['includes']), function ($include) use ($allowedIncludes) {
            return in_array(explode('.', $include)[0], $allowedIncludes);
        });
    }

    /**
     * @param $object
     * @param $format
     * @param array $context
     * @return array|array[]|ArrayObject[]|bool[]|float[]|int[]|null[]|string[]
     * @throws ExceptionInterface
     */
    private function getIncludes($object, $format, array $context)
    {
        return array_map(function ($root) use ($object, $format, $context) {
            return $this->normalizer->normalize(
                $this->getSubObject($object, $root),
                $format,
                $this->getSubContext($context, $root)
            );
        }, $this->getRootIncludes($context));
    }

    /**
     * Build an array with key and value is subObject needed for array_map
     * (in this case ["adresse" => "adresse"])
     *
     * @param array $context
     * @return array
     */
    private function getRootIncludes(array $context): array
    {
        return array_reduce($context['query']['includes'], function ($carry, $include) {
            $rootInclude = explode('.', $include)[0];
            return $carry + [$rootInclude => $rootInclude];
        }, []);
    }

    private function getSubContext(array $context, string $root): array
    {

        /**
         * Filter only value with pattern $root . '.'
         * @var array $subContextWithRoot
         */
        $subContextWithRoot = array_filter($context['query']['includes'], function ($subInclude) use ($root) {
            return strpos($subInclude, $root . '.') === 0;
        });


        /**
         * Extract fields.
         * @var array $subContext
         */
        $subContext = array_map(function ($subContextWithRoot) {
            return substr($subContextWithRoot, strpos($subContextWithRoot, '.') + 1);
        }, $subContextWithRoot);

        /**
         * Add key root into context with fields.
         */
        $context['query']['fields'][$root] = $subContext;

        return $context;
    }

    /**
     * Get value of sub-object
     * @param $object
     * @param string $include
     * @return mixed|null
     */
    private function getSubObject($object, string $include)
    {
        return $this->propertyAccessor->getValue($object, $include);
    }
}
