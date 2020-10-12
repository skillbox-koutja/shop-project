<?php

declare(strict_types=1);

namespace App\Security\Voter\Shop;

use App\Model\Shop\Entity\Product\Product;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductAccess  extends Voter
{
    public const VIEW = 'product/view';
    public const MANAGE = 'product/edit';
    public const DELETE = 'product/delete';

    private AuthorizationCheckerInterface $security;
    private array $actions;

    public function __construct(AuthorizationCheckerInterface $security)
    {
        $this->security = $security;
        $this->actions = [self::VIEW, self::MANAGE, self::DELETE];
    }

    protected function supports($attribute, $subject): bool
    {
        $isAction = in_array($attribute, $this->actions, true);
        if (!$isAction) {
            return false;
        }
        if (!$subject) {
            return true;
        }

        return $subject instanceof Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserIdentity) {
            return false;
        }

        return $this->isGranted();
    }

    private function isGranted(): bool
    {
        return $this->security->isGranted('ROLE_SHOP_MANAGE_PRODUCTS');
    }
}
