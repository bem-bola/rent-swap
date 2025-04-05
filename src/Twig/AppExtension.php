<?php

namespace App\Twig;

use App\Entity\TypeUser;
use App\Entity\User;
use IntlDateFormatter;
use NumberFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
public function getFilters(): array
{
    return [
        new TwigFilter('filterUser', [$this, 'excludeCurrentUser']),
        new TwigFilter('firstFilename', [$this, 'firstFilename']),
        new TwigFilter('typeUserFrench', [$this, 'typeUserFrench']),
        new TwigFilter('formatDateFr', [$this, 'formatDateFr']),
        new TwigFilter('replacePage', [$this, 'replacePage']),
    ];
}

    public function excludeCurrentUser(iterable $users, int $userId): ?User
    {
        $filterUser = array_filter($users->toArray(), fn($user) => $user->getId() !== $userId);

        return $filterUser == [] ? null : array_pop($filterUser);
    }

    public function firstFilename(iterable $pictures): ?string
    {
        if($pictures->toArray()) return $pictures[0]->getFilename();
        return 'logo-reusiix.svg';
    }

    public function typeUserFrench(?TypeUser $typeUser): ?string
    {
        $typesEng = [
            'professional' => 'professionnel',
            'particular' => 'particulier',
            'organization' => 'association'
        ];
        return $typesEng[$typeUser->getName()] ?? null;
    }

    public function formatDateFr(\DateTime $date): ?string
    {
        $formatter = new IntlDateFormatter(
            'fr_FR', // locale française
            IntlDateFormatter::LONG,
            IntlDateFormatter::SHORT
        );

        return $formatter->format($date);
    }

    public function replacePage(string $url, ?int $pageCurrent = null, ?int $page = null, string $sort = null): string
    {
        $parts = parse_url(urldecode($url));
        $query = $parts['query'] ?? '';

        $query = $query != null ? str_replace("pagination[page]=$pageCurrent", "", $query) : '';
        $query = $query != null ? str_replace("orderby[price]=asc", "", $query) : '';
        $query = $query != null ? str_replace("orderby[price]=desc", "", $query) : '';

        $page = $page ?? 1;

        return $query != null ? "?{$query}&pagination[page]=$page&orderby[price]=$sort" : '';

    }

}