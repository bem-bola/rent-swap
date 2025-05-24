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
     * @param string $baseSql
     * @param array $queryParams
     * @param bool $count
     * @return array|int|null
     * @throws Exception
     */
    public function getDatasPaginator(
        ResultSetMapping $resultSetMapping,
        string           $baseSql,
        array            $queryParams = [],
        bool             $count = false): array|int|null
    {
        // parametre url
        $parameters = [];
        // ajoute filtre
        if(isset($queryParams['filters']))
            $this->addFiltersDevice($baseSql, $queryParams['filters'], $parameters);

        $totalResult = $this->countDatas($baseSql, $parameters);


        if($count === true) return $totalResult;


        $limit = $queryParams['pagination']['limit'] ?? 10;
        $page =  $queryParams['pagination']['page'] ?? 1;
        $offset = ($page-1)*$limit;


        $parameters['limit']  = (int)$limit;
        $parameters['offset'] = (int)$offset;


        $query = $this->em->createNativeQuery("$baseSql LIMIT :limit OFFSET :offset", $resultSetMapping);

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
    public function dataDataBleDeviceBySql(ResultSetMapping $resultSetMapping, string $baseSql, array $queryParams = [], bool $count = false): array
    {

        $parameters = [];

        $this->addFiltersDevice($baseSql, $queryParams['search'], $parameters);

        $totalResult = $this->countDatas($baseSql, $parameters);

        if($count === true) return $totalResult;

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

    public function dataTableByQueryBuilder($query, $queryParams): array
    {
        $this->addFilters($query, $queryParams['search'] ?? []);

        $data =  $this->paginator->paginate(
            $query,
            intval($queryParams['page'] ?? 1),
            intval($queryParams['limit'] ?? 10));


        return [
            'draw' => $queryParams['draw'] ?? 1,
            'page' => $queryParams['page'] ?? 1,
            'recordsTotal' => $data->getTotalItemCount(),
            'recordsFiltered' => $data->getTotalItemCount(),
            'data' => $data->getItems()
        ];

    }


    private function addFilters(&$query, array $filters): void{

        if($filters != null) {
            foreach ($filters as $filter => $value) {

                if($value != null){

                    if($filter == 'u.status'){
                        $this->addFiltersStatusUser($query, $value);
                    }else{
                        // enlever le "." ex: c.name en name
                        $columnName = strrchr($filter, '.');
                        $columnName = $columnName ? str_replace('.', '', $columnName) : $filter;

                        $query->andWhere("LOWER($filter) LIKE :$columnName");
                        $query->setParameter("$columnName", "%" . mb_strtolower($value) . "%");
                    }

                }

            }
        }

    }

    private function addFiltersStatusUser(&$query, string $value): void{

        switch ($value) {
            case Constances::BANNED:
                $query->andWhere('u.isBanned = true');
                break;

            case Constances::SUSPENDED:
                $query->andWhere('u.isSuspended = true');
                break;

            case Constances::DELETED:
                $query->andWhere('u.isDeleted = true');
                break;

            case Constances::VALIDED:
                $query->andWhere('u.isVerified = true');
                break;

            case Constances::NOVALIDED:
                $query->andWhere('u.isVerified = false');
                break;
        }
    }

    /**
     * @param string $sql
     * @param array $filters
     * @param array $parameters
     * @return void
     */
    private function addFiltersDevice(string &$sql, array $filters, array &$parameters): void
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
                    elseif($filter === 'category'){
                        $sql .= " AND c.slug = :$filter ";
                        $parameters[$filter] = $value;
                    }
                    elseif($filter === 'priceMin'){
                        $sql .= " AND d.price >= :$filter ";
                        $parameters[$filter] = (int)$value;
                    }elseif($filter === 'priceMax'){
                        $sql .= " AND d.price <= :$filter ";
                        $parameters[$filter] = $value;
                    }else{
                        $sql .= " AND d.$filter = :$filter ";
                        $parameters[$filter] = strtolower($value);
                    }
                }
            }
        }

    }

}