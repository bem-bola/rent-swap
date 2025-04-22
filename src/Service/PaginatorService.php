<?php

namespace App\Service;


use App\Entity\Device;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class PaginatorService {

    public function __construct(
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator)
    {}
    public function getDatas($query, ?array $pagination = null): array
    {

        $data =  $this->paginator->paginate(
            $query,
            $pagination['page'] ?? 1,
            $pagination['limit'] ?? 10);

        return [
            'pagination' => [
                'total' => $data->getTotalItemCount(),
                'page' => $data->getCurrentPageNumber(),
                'limit_per_page' => $data->getItemNumberPerPage(),
                'total_pages' => $data->getPaginationData()['pageCount'],
                'nb_data_current_page' => $data->getPaginationData()['currentItemCount'],
            ],
            'items' => $data->getItems(),
        ];
    }

    /**
     * @throws Exception
     */
    private function countDatas(string $sql, array $parameters){
        return $this->em
            ->getConnection()
            ->prepare("SELECT COUNT(*) FROM ($sql) as tab")
            ->executeQuery($parameters)
            ->fetchOne();
    }

    /**
     * @throws Exception
     */
    public function getDatasPaginator(ResultSetMapping $resultSetMapping, $baseSql, array $parameters = [], ?array $pagination = null): array
    {

        $sql =  "$baseSql LIMIT :limit OFFSET :offset";

        $limit = $pagination['limit'] ?? 10;
        $page =  $pagination['page'] ?? 1;
        $offset = ($page-1)*$limit;
        $totalResult = $this->countDatas($baseSql, $parameters);

        $parameters['limit'] = $limit;
        $parameters['offset'] = (int)$offset;

        $query = $this->em->createNativeQuery($sql, $resultSetMapping);

        $query->setParameters($parameters);

        return [
            'pagination' => [
                'total' => $totalResult,
                'page' => $page,
                'limit_per_page' => $limit,
                'total_pages' => $totalResult > 0 ? ceil($totalResult/$limit) : 0,
            ],
            'items' => $query->getResult()
        ];
    }

}