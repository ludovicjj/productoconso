<?php

namespace App\Voter;

use App\Entity\Producer;
use App\Entity\Product;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProductVoter
 * @package App\Voter
 */
class ProductVoter extends Voter
{
    public const UPDATE = "update";

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::UPDATE])) {
            return false;
        }

        // only vote on `Product` objects
        if (!$subject instanceof Product) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $producer = $token->getUser();

        if (!$producer instanceof Producer) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Product $product */
        $product = $subject;

        switch ($attribute) {
            case self::UPDATE:
                return $this->canUpdate($product, $producer);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canUpdate(Product $product, Producer $producer): bool
    {
        return $product->getFarm()->getProducer() === $producer;
    }
}
