<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use Psr\Http\Message\ResponseInterface as Response;

class ListTasksAction extends TaskAction
{
    /**
     * default page
     */
    private const DEFAULT_PAGE = 1;
    
    /**
     * default limit per page if a user doesn't pass the limit on the URI
     */
    private const DEFAULT_LIMIT = 15;
    
    /**
     * max limit per page
     */
    private const MAX_LIMIT = 50;

    /** {@inheritdoc} */
    protected function action(): Response
    {
        $queryParams = $this->request->getQueryParams();

        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : self::DEFAULT_PAGE;
        $limit = isset($queryParams['limit']) ? (int)$queryParams['limit'] : self::DEFAULT_LIMIT;

        // at least get 1 as page
        $page = max(1, $page);

        // limit should not be greater than max limit
        $limit = max(1, min($limit, self::MAX_LIMIT));

        $totalTasks = $this->taskRepository->countAll();

        $totalPages = $totalTasks > 0 ? (int)ceil($totalTasks / $limit) : 1;

        // calculate the offset
        $offset = ($page - 1) * $limit;

        $tasks = $this->taskRepository->findAll($limit, $offset);

        // get the base url
        $baseUrl = (string)$this->request->getUri()->withQuery('')->withFragment('');

        // setup the structured data with meta fields
        $paginationData = [
            'data' => $tasks,
            'meta' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalTasks,
                'last_page' => $totalPages,
                'from' => $totalTasks > 0 ? $offset + 1 : null,
                'to' => $totalTasks > 0 ? min($offset + $limit, $totalTasks) : null,
            ],
            'links' => [
                'first' => $baseUrl . '?' . http_build_query(['page' => 1, 'limit' => $limit]),
                'last' => $baseUrl . '?' . http_build_query(['page' => $totalPages, 'limit' => $limit]),
                'prev' => ($page > 1) ? $baseUrl . '?' . http_build_query(['page' => $page - 1, 'limit' => $limit]) : null,
                'next' => ($page < $totalPages) ? $baseUrl . '?' . http_build_query(['page' => $page + 1, 'limit' => $limit]) : null,
            ],
        ];

        $this->logger->info("Tasks list was viewed (Page: {$page}, Limit: {$limit}).");
        
        return $this->respondWithData($paginationData);
    }
}