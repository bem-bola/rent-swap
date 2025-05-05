<?php

namespace App\Twig;

use App\Entity\Device;
use App\Entity\Favorite;
use App\Entity\TypeUser;
use App\Entity\User;
use App\Repository\DevicePictureRepository;
use App\Repository\DeviceRepository;
use IntlDateFormatter;
use NumberFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    public function __construct(
        private readonly DeviceRepository $deviceRepository,
        private readonly DevicePictureRepository $devicePictureRepository)
    {

    }
public function getFilters(): array
{
    return [
        new TwigFilter('filterUser', [$this, 'excludeCurrentUser']),
        new TwigFilter('firstFilename', [$this, 'firstFilename']),
        new TwigFilter('typeUserFrench', [$this, 'typeUserFrench']),
        new TwigFilter('formatDateFr', [$this, 'formatDateFr']),
        new TwigFilter('replacePage', [$this, 'replacePage']),
        new TwigFilter('pathAvatarUser', [$this, 'pathAvatarUser']),
        new TwigFilter('getDevice', [$this, 'getDevice']),
    ];
}

    public function excludeCurrentUser(iterable $users, int $userId): ?User
    {
        $filterUser = array_filter($users->toArray(), fn($user) => $user->getId() !== $userId);

        return $filterUser == [] ? null : array_pop($filterUser);
    }

    public function firstFilename(string $slug): ?string
    {
        $device = $this->deviceRepository->findOneBy(['slug' => $slug]);
        $picture = $this->devicePictureRepository->findByParentFirst($device);

        if($picture) return $picture->getFilename();

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

    public function replacePage(
        string $url,
        ?int $page = null,
        string $sort = null,
        string $status = null,
        string $title = null): string
    {

        $parts = parse_url(urldecode($url));
        parse_str($parts['query'] ?? '', $queryParams);

        // Nettoyage des anciens paramètres
        $queryParams['pagination']['page'] = $page != null ? $page : 1;
        // Supprimer les valeurs spécifiques de tri
        $queryParams['orderby']['price'] = $sort != null ? $sort : '';
        // Supprimer les statuts spécifiques
        $queryParams['filters']['status'] = $status != null ? $status : '';

        $queryParams['filters']['title'] = $title != null ? $title : '';

        return '?' . http_build_query($queryParams);

    }

    public function pathAvatarUser(User $user): string
    {
        if($user->getAvatar()) return '/upload/avatars/' . $user->getAvatar()->getFilename();
        else return sprintf("/img/letters/%s.png", substr($user->getFirstname(), 0, 1));
    }

    public function getDevice($iterable): ?Device {
        if($iterable instanceof Favorite){
            return $iterable->getDevice();
        }
        return $iterable;
    }

}