<?php

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\TypeUser;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\TypeUserRepository;
use App\Service\Constances;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserFactory
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerService          $logger,
        private readonly TypeUserRepository     $typeUserRepository
    ) {}

    public function createByUser(User &$user, string $password, ?TypeUser $typeUser = null): User{
        $type = $typeUser ?? $this->typeUserRepository->findOneBy(['name' => 'particular']);
        $user->setPassword($password);
        $user->setCreated(new \DateTime());
        $user->setIsVerified(false);
       $user->setType($type);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->write(
            Constances::LEVEL_INFO,
            "user id: " . $user->getId() . " créé",
            Response::HTTP_CREATED,
            $user
        );

        return $user;
    }

    public function addAvatar(User $user, Media $media): User{
        $user->setAvatar($media);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->write(
            Constances::LEVEL_INFO,
            "Avatar de user id: " . $user->getId() . " modifié",
            Response::HTTP_CREATED,
            $user
        );

        return $user;
    }


    public function updateStatus(User $user, bool $status, ?User $executor): User
    {
        switch ($status) {
            case Constances::DELETED:
                $user->setDeleted(new \DateTime());
                $user->setIsVerified(true);
                break;

            case Constances::BANNED:
                $user->setBanned(new \DateTime());
                $user->setIsBanned(true);
                break;

            case Constances::VALIDED:
                $user->setVerified(new \DateTime());
                $user->setIsVerified(true);
                break;
        }

        $this->logger->write(
            Constances::LEVEL_INFO,
            sprintf("Utilisateur %s modifié action: %s", $user->getId(), $status),
            Response::HTTP_CREATED,
            $executor
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;

    }

    /**
     * @throws \Exception
     */
    public function updateRole(User $user, string $role, User $executor): User{
        if(!in_array($role, Constances::ARRAY_ROLES)){
            $this->logger->write('error', "Role $role undefined", Response::HTTP_NOT_FOUND, $executor);
            throw new \Exception("Role undefined !");
        }

        $user->setRoles([$role]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->write('info', "Role update for user id: " . $user->getId(), Response::HTTP_CREATED, $executor);

        return $user;
    }
}