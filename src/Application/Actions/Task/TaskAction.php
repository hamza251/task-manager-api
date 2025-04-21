<?php

declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\Action;
use App\Domain\Task\TaskRepository;
use Psr\Log\LoggerInterface;

abstract class TaskAction extends Action
{
    
    public function __construct(protected LoggerInterface $logger, protected TaskRepository $taskRepository)
    {
        parent::__construct($logger);
        $this->taskRepository = $taskRepository;
    }
}
