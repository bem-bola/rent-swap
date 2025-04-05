<?php

namespace App\Service;


use Knp\Component\Pager\PaginatorInterface;

class PaginatorService {

    public function __construct(private PaginatorInterface $paginator)
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
}