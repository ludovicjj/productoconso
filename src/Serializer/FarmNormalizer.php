<?php

namespace App\Serializer;

use App\Entity\Farm;
use App\Serializer\Includes\IncludeNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class FarmNormalizer
 * @package App\Serializer
 */
class FarmNormalizer implements ContextAwareNormalizerInterface
{
    /** @var ObjectNormalizer $objectNormalizer */
    private $objectNormalizer;

    private $includeNormalizer;

    public const OBJECT_TYPE = 'farm';

    public const ALLOWED_PUBLIC_ATTRIBUTES = [
        'id',
        'name',
        'slug'
    ];

    public const ALLOWED_INCLUDES = [
        'adresse',
    ];

    public function __construct(
        ObjectNormalizer $objectNormalizer,
        IncludeNormalizer $includeNormalizer
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->includeNormalizer = $includeNormalizer;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Farm;
    }

    public function normalize($farm, string $format = null, array $context = [])
    {
        $context['query']['fields'][self::OBJECT_TYPE][] = 'id';

        $objectNormalizerContext = ['attributes' => []];
        $allowAttributes = $context['query']['fields'][self::OBJECT_TYPE];
        $objectNormalizerContext['attributes'] = $this->filterPublicAttributes($allowAttributes);

        /** @var array $farmNormalized */
        $farmNormalized =  $this->objectNormalizer->normalize($farm, $format, $objectNormalizerContext);

        $includeNormalized = $this->includeNormalizer->normalizeIncludes(
            $farm,
            $format,
            $context,
            self::ALLOWED_INCLUDES
        );

        return array_merge($farmNormalized, $includeNormalized);
    }

    private function filterPublicAttributes(array $attributes): array
    {
        return array_filter(array_unique($attributes), function ($attribute) {
            if (!in_array($attribute, self::ALLOWED_PUBLIC_ATTRIBUTES)) {
                return false;
            } else {
                return true;
            }
        });
    }
}
