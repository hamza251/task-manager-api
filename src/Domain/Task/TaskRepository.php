<?php

declare(strict_types=1);

namespace App\Domain\Task;

interface TaskRepository
{
     /**
     * 
     * @param int|null $limit max number of tasks per page
     * @param int|null $offset number of tasks to skip
     * @return Task[]
     */
    public function findAll(?int $limit = null, ?int $offset = null): array; 

    /**
     * @param int $id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function findTaskById(int $id): Task;

    /**
     * @param string $title
     * @param string|null $description
     * @param bool $completed
     * @return Task newly created task
     */
    public function createTask(string $title, ?string $description, bool $completed): Task;


    /**
     * @param int $id
     * @param string $title
     * @param string|null $description
     * @param bool $completed
     * @return Task updated task
     * @throws TaskNotFoundException
     */
    public function updateTask(int $id, string $title, ?string $description, bool $completed): Task;

    /**
     * @param int $id
     * @return bool true on success, otherwise false on any error
     * @throws TaskNotFoundException
     */
    public function deleteTaskById(int $id): bool;

    /**
     * Count all available tasks.
     * @return int
     */
    public function countAll(): int; // Add this method
}