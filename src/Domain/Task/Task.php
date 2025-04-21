<?php

declare(strict_types=1);

namespace App\Domain\Task;

use JsonSerializable;
use DateTimeImmutable;

class Task implements JsonSerializable
{
    /**
     * 
     */
    public function __construct(
         private ?int $id, // null for new tasks
        private  string $title,
         private ?string $description,
        private  bool $completed,
        private  DateTimeImmutable $createdAt // Accept DateTimeImmutable
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;

        // use setters for doing validation or any other logics
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setCompleted($completed);
    }

    /**
     * Getter: get the ID
     * 
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter: get title
     * 
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Getter: get description
     * 
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Getter: get completed status
     * 
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * Getter: get creation date
     * 
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter: set title
     * 
     * @return void
     */
    public function setTitle(string $title): void
    {
        // basic validation for checking up characters
        if (strlen(trim($title)) < 3) {
            // @todo throw an exception
            // though this logic is already being handled in Action class.
        }
        $this->title = trim($title);
    }

    /**
     * Setter: set completed
     * 
     * @return void
     */
    public function setCompleted(bool $completed): void
    {
         $this->completed = $completed;
    }

    /**
     * Setter: set description
     * 
     * @return void
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


    /**
     * return structured array
     */
    #[\ReturnTypeWillChange] // Add this attribute for PHP 8.1+ compatibility
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'created_at' => $this->createdAt->format(DateTimeImmutable::ATOM),
        ];
    }
}