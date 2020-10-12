<?php

declare(strict_types=1);

namespace App\Security\Voter\Shop;

use App\Model\Shop\Entity\Order\Order;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderAccess  extends Voter
{
    public const VIEW = 'view';
    public const MANAGE = 'edit';
    public const DELETE = 'delete';

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

        return $subject instanceof Order;
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
        return $this->security->isGranted('ROLE_SHOP_MANAGE_ORDERS');
    }
}
