<?php

namespace App\Service;

use App\Repository\BaseRepository;
use Symfony\Component\HttpFoundation\Request;

class PaginatorService
{
    public function initPagination(BaseRepository $repository, int $limit, string $alias, ?string $orderBy = null, Request $request): array
    {
        $page = max(1, $request->query->getInt('page', 1));
        $items = $repository->paginate(
            page: $page,
            limit: $limit,
            alias: $alias,
            orderBy: $orderBy
        );
        $maxPage = max(1, ceil($items->count() / $limit));

        return [
            'page' => $page,
            'items' => $items,
            'maxPage' => $maxPage
        ];
    }
}
