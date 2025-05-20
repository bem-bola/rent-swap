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
     * @param string $sql
     * @param array $parameters
     * @return false|mixed
     */
    private function countDatas(string $sql, array $parameters): mixed
    {
        return $this->em
            ->getConnection()
            ->prepare("SELECT COUNT(*) FROM ($sql) as tab")
            ->executeQuery($parameters)
            ->fetchOne();
    }


    /**
     * @param ResultSetMapping $resultSetMapping
     * @param $baseSql
     * @param array $parameters
     * @param array|null $pagination
     * @param bool $count
     * @return array|int|null
     * @throws Exception
     */
    public function getDatasPaginator(
        ResultSetMapping $resultSetMapping,
        $baseSql,
        array $parameters = [],
        ?array $pagination = null,
        bool $count = false): array|int|null
    {

        $totalResult = $this->countDatas($baseSql, $parameters);

        if($count === true) return $totalResult;
        $sql =  "$baseSql LIMIT :limit OFFSET :offset";


        $limit = $pagination['limit'] ?? 10;
        $page =  $pagination['page'] ?? 1;
        $offset = ($page-1)*$limit;


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

    /**
     * @throws Exception
     */
    public function getDataDataTable(ResultSetMapping $resultSetMapping, string $baseSql, array $queryParams = []): array
    {

        $parameters = [];

        $this->addFilters($baseSql, $queryParams['search'], $parameters);

        $totalResult = $this->countDatas($baseSql, $parameters);

        $sql =  "$baseSql LIMIT :limit OFFSET :offset";

        $parameters['limit'] = intval($queryParams['limit'] ?? 10);
        $parameters['offset'] = intval($queryParams['offset'] ?? 1);


        $query = $this->em->createNativeQuery($sql, $resultSetMapping);

        $query->setParameters($parameters);

        return [
            'draw' => $queryParams['draw'] ?? 1,
            'page' => $queryParams['page'] ?? 1,
            'recordsTotal' => $totalResult,
            'recordsFiltered' => $totalResult,
            'data' => $query->getResult()
        ];

    }

    /**
     * @param string $sql
     * @param array $filters
     * @param array $parameters
     * @return void
     */
    private function addFilters(string &$sql, array $filters, array &$parameters): void
    {
        if($filters != null) {
            foreach ($filters as $filter => $value) {
                if($value != null){
                    if($filter === 'title'){
                        $sql .= " AND LOWER(d.$filter) LIKE :$filter ";
                        $parameters[$filter] = '%' . strtolower($value) . '%';
                    }elseif(in_array($filter, ['parent_id', 'user_id'])){
                        $sql .= " AND d.$filter = :$filter ";
                        $parameters[$filter] = $value;
                    }
                    else{
                        $sql .= " AND d.$filter = :$filter ";
                        $parameters[$filter] = strtolower($value);
                    }
                }
            }
        }

    }

}