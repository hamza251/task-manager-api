<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskNotFoundException;
use App\Domain\Task\TaskRepository;
use PDO; // Use PDO directly
use PDOException;
use DateTimeImmutable;

class DatabaseTaskRepository implements TaskRepository
{

    /**
     * 
     */
    public function __construct(protected PDO $db) {}

    public function findAll(
        ?int $limit = null,
        ?int $offset = null
    ): array {

        $sql = 'SELECT id, title, description, completed, created_at FROM tasks ORDER BY created_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT :limit';
            if ($offset !== null) {
                $sql .= ' OFFSET :offset';
            }
        }

        $stmt = $this->db->prepare($sql);

        // bind limit and offset
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        $tasksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tasks = [];
        foreach ($tasksData as $data) {
            $tasks[] = $this->mapDataToTask($data);
        }
        return $tasks;
    }

    public function findTaskById(int $id): Task
    {
        $stmt = $this->db->prepare(
            'SELECT id, title, description, completed, created_at FROM tasks WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC); // transform to associative array

        if (!$data) {
            throw new TaskNotFoundException();
        }

        return $this->mapDataToTask($data);
    }

    /**
     * create a task
     * 
     * 
     */
    public function createTask(string $title, ?string $description, bool $completed): Task
    {
        $sql = "INSERT INTO tasks (title, description, completed) VALUES (:title, :description, :completed)";
        $stmt = $this->db->prepare($sql);

        // bind params
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindValue(':completed', $completed, PDO::PARAM_BOOL);

        $stmt->execute();

        // get last inserted id
        $lastId = (int)$this->db->lastInsertId('tasks_id_seq');

        // fetch the created task
        return $this->findTaskById($lastId);
    }

    /**
     * update a task
     * 
     */
    public function updateTask(int $id, string $title, ?string $description, bool $completed): Task
    {
        // check if the task exists already, otherwise throws TaskNotFoundException   
        $this->findTaskById($id);

        $sql = "UPDATE tasks SET title = :title, description = :description, completed = :completed WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        // bind params
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindValue(':completed', $completed, PDO::PARAM_BOOL);

        $stmt->execute();

        // fetch the updated task
        return $this->findTaskById((int) $id);
    }

    /**
     * delete a task
     */
    public function deleteTaskById(int $id): bool
    {
        // check if the task exists already, otherwise throws TaskNotFoundException   
        $this->findTaskById($id);

        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // check if any row was affected, means deleted then return true
        return $stmt->rowCount() > 0;
    }

    /**
     * Helper to map database row array to Task object
     */
    private function mapDataToTask(array $data): Task
    {
        return new Task(
            (int)$data['id'],
            $data['title'],
            $data['description'],
            (bool)$data['completed'],
            new DateTimeImmutable($data['created_at'])
        );
    }

    /**
     * 
     */
    public function countAll(): int
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM tasks');
        return (int)$stmt->fetchColumn(); 
    }
}
