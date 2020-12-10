<?php


namespace App\Serializer;

use App\Entity\Adresse;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AdresseNormalizer implements ContextAwareNormalizerInterface
{
    /** @var ObjectNormalizer $objectNormalizer */
    private $objectNormalizer;

    const OBJECT_TYPE = 'adresse';

    const ALLOWED_PUBLIC_ATTRIBUTES = [
        'adresse',
        'zipCode',
        'city'
    ];

    public function __construct(
        ObjectNormalizer $objectNormalizer
    ) {
        $this->objectNormalizer = $objectNormalizer;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Adresse;
    }

    public function normalize($adresse, string $format = null, array $context = [])
    {
        $objectNormalizerContext = ['attributes' => []];
        $allowAttributes = $context['query']['fields'][self::OBJECT_TYPE];
        $objectNormalizerContext['attributes'] = $this->filterPublicAttributes($allowAttributes);

        return  $this->objectNormalizer->normalize($adresse, $format, $objectNormalizerContext);
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
