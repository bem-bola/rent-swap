<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 */
class CityRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $em;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($registry, City::class);
    }

    /**
     * @throws Exception
     */
    public function getCities(string $city =  '')
    {
        $sql = 'SELECT code_postal, name FROM city WHERE LOWER(name) LIKE :query ';

        return $this->getEntityManager()
            ->getConnection()
            ->prepare($sql)
            ->executeQuery(['query' => "%" . strtolower($city). "%"])
            ->fetchAllAssociative();
    }
}
