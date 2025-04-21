# Task Manager API - SlimPHP + PostgreSQL Test

A simple Restful API built on the top of SlimPHP Skeleton with PgSQL.

## Requirements

*   Docker
*   Docker Compose

## Setup & Running

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/hamza251/task-manager-api.git task-manager-api
    cd task-manager-api
    ```

2.  **Environment Variables:**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Review the `.env` file. The default values should work with the provided `docker-compose.yml`.

3.  **Build and Start Containers:**
    ```bash
    docker-compose up -d --build
    ```
    .

4.  **Access the API:**
    The API will be available at `http://localhost:8080`.

## API Endpoints


*   **`GET /tasks`**: List all tasks.
*   **`POST /tasks`**: Create a new task.
    *   Request Body (JSON): `{"title": "Task Title", "description": "Optional description", "completed": false}`
    *   `title` is required (min 3 characters). `description` is optional. `completed` defaults to `false` if omitted.
*   **`GET /tasks/{id}`**: Get a single task by its ID.
*   **`PUT /tasks/{id}`**: Update an existing task.
    *   Request Body (JSON): `{"title": "Updated Title", "description": "Updated description", "completed": true}`
    *   All fields (`title`, `description`, `completed`) are required for PUT requests in this implementation. *(Adjust this note if your PUT implementation allows partial updates)*. `title` must be min 3 characters.
*   **`DELETE /tasks/{id}`**: Delete a task by its ID.

## Stopping the Application

```bash
docker-compose down```
To remove the persistent database volume as well (deletes all task data):
```bash
docker-compose down --volumes