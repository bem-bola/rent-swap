<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
public function getFilters(): array
{
    return [
        new TwigFilter('filterUser', [$this, 'excludeCurrentUser']),
    ];
}

    public function excludeCurrentUser(iterable $users, int $userId): ?User
    {
        $filterUser = array_filter($users->toArray(), fn($user) => $user->getId() !== $userId);

        return $filterUser == [] ? null : array_pop($filterUser);
    }
}