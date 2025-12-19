
Thank you for taking the time to complete this assessment.  
The goal is to demonstrate how you approach architecture, testing, and backend engineering best practices.

## 📖 Project: Collaborative Task Management API

### Objective
Build a REST API for managing projects, tasks, comments, and notifications.  
We value **clean architecture, thoughtful design, and code quality** over speed or feature quantity.

---

## ✅ Requirements

### Core Features
- **Authentication**: User registration & login (JWT or Laravel Sanctum).
- **Projects**: CRUD operations. Each project belongs to a user.
- **Tasks**:
  - CRUD operations.
  - Fields: `title, description, status (todo/in-progress/done), due_date`.
  - Filtering: by status, due date, full-text search.
  - Pagination for listing.
- **Comments**: CRUD operations. Each comment belongs to a task.
- **Notifications**:
  - Triggered when a task is assigned or updated.
  - Delivered asynchronously (e.g., queue).
  - Endpoint for fetching unseen notifications.

### Non-Functional
- Use a layered architecture (controllers, services, repositories, domain models).
- Apply at least two meaningful design patterns (e.g., Repository, Strategy, Observer).
- Database migrations must be included.
- Cache task listings (e.g., Redis).
- Add rate limiting for sensitive endpoints.
- Standardized error handling and responses.

### Testing
- Unit tests for core services and repositories.
- Integration tests for API endpoints.
- Minimum **70% test coverage**.

### DevOps
- `Dockerfile` + `docker-compose.yml` for local setup.
- CI pipeline runs automatically (tests, static analysis, linting, security).
- Compatible with **PHP 8.2+**.

### Documentation
- Update this `README.md` to include:
  - Setup instructions.
  - Example API requests (curl/Postman).
  - Explanation of your architectural decisions and trade-offs.
  - Which design patterns you applied, and why.

---

## 🎯 Acceptance Criteria

Your submission will be evaluated on:

- **Architecture & Patterns**: Separation of concerns, justified design patterns.
- **Code Quality & Standards**: PSR-12 compliance, maintainability.
- **Feature Completeness**: Requirements implemented.
- **Testing**: Coverage, meaningful cases, edge-case handling.
- **Documentation**: Clear and professional.
- **DevOps**: CI/CD awareness, Docker setup.

---

## 📝 Commit Guidelines

We value not only the final code but also how you structure your work.  
Please use **meaningful, structured commit messages** throughout your development.  

- Follow [Conventional Commits](https://www.conventionalcommits.org/) style when possible:  
  - `feat:` – for new features  
  - `fix:` – for bug fixes  
  - `chore:` – for setup, configuration, or maintenance  
  - `test:` – for adding or improving tests  
  - `docs:` – for documentation changes  

- Examples:  
  - `chore: initial commit (Laravel project setup)`  
  - `feat: add task CRUD endpoints`  
  - `fix: correct due date validation logic`  

Your commit history will be reviewed as part of the assessment to understand how you approach iteration, problem-solving, and communication through code.


## 📦 Submission Instructions
1. Implement your solution inside this repo.
2. Push to a private GitHub repository.
3. Invite the following reviewers with **Read**  role: `gh-ewmateam`.
4. Please complete within 7 days of receiving the assignment.
5. If you need more time, let us know.

## ℹ️ Notes
1. The project is designed to take 3–5 hours. We do not expect a production-ready system.
2. Quality matters more than quantity — partial solutions are acceptable if well-documented.
3. Document anything you would do differently with more time.

---

## 🚀 Setup Instructions

To get the project up and running locally using Docker:

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd task-api-template
    ```

2.  **Copy the environment file:**
    ```bash
    cp .env.example .env
    ```
    (Make sure to configure your database credentials and other environment variables in `.env` if needed, though Docker Compose will set up MySQL and Redis for you.)

3.  **Build and start the Docker containers:**
    ```bash
    docker-compose up -d --build
    ```

4.  **Install Composer dependencies:**
    ```bash
    docker-compose exec laravel.test composer install
    ```

5.  **Generate application key:**
    ```bash
    docker-compose exec laravel.test php artisan key:generate
    ```

6.  **Run database migrations:**
    ```bash
    docker-compose exec laravel.test php artisan migrate
    ```

7.  **Run the queue worker (for notifications):**
    ```bash
    docker-compose exec laravel.test php artisan queue:work &
    ```
    (You might want to run this in a separate terminal or use a process manager like Supervisor in production.)

The API should now be accessible at `http://localhost` (or the `APP_PORT` defined in your `.env` file).

## 💡 Architectural Decisions and Trade-offs

1.  **Layered Architecture (Controllers, Services, Repositories)**:
    *   **Decision**: Implemented a clear separation of concerns by introducing Service and Repository layers.
    *   **Justification**:
        *   **Controllers**: Handle HTTP requests, validation, and delegate business logic to services. They remain thin.
        *   **Services**: Contain the core business logic, orchestrating interactions between repositories and other components (e.g., notifications, caching). This promotes reusability and testability of business rules.
        *   **Repositories**: Abstract the data persistence layer, providing a clean API for interacting with models and the database. This makes the application less dependent on a specific ORM or database system.
    *   **Trade-offs**: Increased boilerplate code (more files, more classes) compared to a simpler MVC approach. However, this pays off in larger, more complex applications by improving maintainability, testability, and scalability.

2.  **Laravel Sanctum for Authentication**:
    *   **Decision**: Used Laravel Sanctum for API token-based authentication.
    *   **Justification**: Provides a simple, robust, and secure way to issue API tokens for SPAs and mobile applications. It's well-integrated with Laravel's authentication system.
    *   **Trade-offs**: While suitable for API tokens, it's not a full-fledged OAuth2 server. For more complex authorization scenarios (e.g., third-party integrations), Laravel Passport might be a better fit.

3.  **Redis for Caching Task Listings**:
    *   **Decision**: Implemented Redis caching for the `TaskController@index` method.
    *   **Justification**: Improves performance by reducing database load for frequently accessed task listings, especially with various filtering options. Redis is a fast in-memory data store ideal for caching.
    *   **Invalidation Strategy**: Instead of complex wildcard invalidation, a `tasks_last_updated_at` timestamp was added to the `projects` table. This timestamp is updated whenever a task within that project is created, updated, or deleted. The cache key for task listings includes this timestamp, ensuring that any change to a project's tasks automatically invalidates the relevant cache entries.
    *   **Trade-offs**: Adds an external dependency (Redis). Cache invalidation can be tricky; the chosen strategy is effective but requires careful management of the `tasks_last_updated_at` field.

4.  **Laravel Notifications for Asynchronous Delivery**:
    *   **Decision**: Utilized Laravel's built-in Notification system with the `ShouldQueue` interface for asynchronous delivery.
    *   **Justification**: Decouples the notification sending process from the main request-response cycle, improving API response times. Notifications are stored in the database and processed by a queue worker, ensuring reliability.
    *   **Trade-offs**: Requires a queue driver (e.g., Redis, database) and a running queue worker. Adds complexity to the deployment and monitoring process.

5.  **Standardized Error Handling**:
    *   **Decision**: Customized `app/Exceptions/Handler.php` to provide consistent JSON error responses for common HTTP exceptions (validation, authentication, authorization, not found).
    *   **Justification**: Ensures a predictable API experience for consumers, making it easier to integrate with the API and handle errors gracefully on the client side.
    *   **Trade-offs**: Requires manual mapping of exceptions to custom responses, but Laravel's `Handler` class provides a convenient place for this.

## 🎨 Design Patterns Applied

1.  **Repository Pattern**:
    *   **Application**: Implemented `ProjectRepository`, `TaskRepository`, and `CommentRepository`.
    *   **Why**: This pattern abstracts the data layer, providing a clean API for data access. It decouples the application's business logic from the persistence logic (Eloquent ORM in this case). This makes the application more testable (can easily mock repositories) and flexible (can swap out ORMs or database systems with minimal impact on the service layer).

2.  **Observer Pattern (via Laravel Notifications)**:
    *   **Application**: The `TaskUpdated` notification, dispatched when a task's status changes, acts as an observer. The `User` model, by using the `Notifiable` trait, becomes the subject that can be observed.
    *   **Why**: This pattern allows objects (notifications) to notify other objects (users) about changes in their state (task updates) without being tightly coupled. When a task is updated, the `TaskUpdated` notification is "observed" by the `User` model, which then processes and stores the notification. This promotes loose coupling and makes it easy to add new ways to react to task updates (e.g., email, Slack) without modifying the core task update logic.

## 📋 Example API Requests (using `curl`)

First, ensure your Docker containers are running (`docker-compose up -d`).

### 1. User Registration

```bash
curl -X POST http://localhost/api/register \
     -H "Content-Type: application/json" \
     -d '{
           "name": "John Doe",
           "email": "john.doe@example.com",
           "password": "password",
           "password_confirmation": "password"
         }'
```

### 2. User Login

```bash
curl -X POST http://localhost/api/login \
     -H "Content-Type: application/json" \
     -d '{
           "email": "john.doe@example.com",
           "password": "password"
         }'
# Expected response will include an access_token. Copy this token for subsequent requests.
```

### 3. Get Authenticated User (requires token)

Replace `<YOUR_ACCESS_TOKEN>` with the token obtained from login.

```bash
curl -X GET http://localhost/api/user \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 4. Create a Project (requires token)

```bash
curl -X POST http://localhost/api/projects \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>" \
     -H "Content-Type: application/json" \
     -d '{
           "name": "My First Project",
           "description": "This is a description for my first project."
         }'
# Note the 'id' of the created project for later requests.
```

### 5. Get All Projects for User (requires token)

```bash
curl -X GET http://localhost/api/projects \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 6. Get a Specific Project (requires token)

Replace `<PROJECT_ID>` with the actual project ID.

```bash
curl -X GET http://localhost/api/projects/<PROJECT_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 7. Update a Project (requires token)

Replace `<PROJECT_ID>` with the actual project ID.

```bash
curl -X PUT http://localhost/api/projects/<PROJECT_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>" \
     -H "Content-Type: application/json" \
     -d '{
           "name": "My Updated Project Name"
         }'
```

### 8. Delete a Project (requires token)

Replace `<PROJECT_ID>` with the actual project ID.

```bash
curl -X DELETE http://localhost/api/projects/<PROJECT_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 9. Create a Task (requires token)

Replace `<PROJECT_ID>` with the actual project ID.

```bash
curl -X POST http://localhost/api/projects/<PROJECT_ID>/tasks \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>" \
     -H "Content-Type: application/json" \
     -d '{
           "title": "Implement Authentication",
           "description": "Set up Laravel Sanctum for user login and registration.",
           "status": "in-progress",
           "due_date": "2024-12-31"
         }'
# Note the 'id' of the created task for later requests.
```

### 10. Get Tasks for a Project (requires token, with filtering/pagination)

Replace `<PROJECT_ID>` with the actual project ID.

```bash
# All tasks for a project
curl -X GET http://localhost/api/projects/<PROJECT_ID>/tasks \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"

# Filter by status
curl -X GET "http://localhost/api/projects/<PROJECT_ID>/tasks?status=todo" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"

# Filter by due date
curl -X GET "http://localhost/api/projects/<PROJECT_ID>/tasks?due_date=2024-12-31" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"

# Search by title/description
curl -X GET "http://localhost/api/projects/<PROJECT_ID>/tasks?search=authentication" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"

# With pagination
curl -X GET "http://localhost/api/projects/<PROJECT_ID>/tasks?page=1&per_page=5" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 11. Get a Specific Task (requires token)

Replace `<TASK_ID>` with the actual task ID.

```bash
curl -X GET http://localhost/api/tasks/<TASK_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 12. Update a Task (requires token)

Replace `<TASK_ID>` with the actual task ID. This will trigger a notification.

```bash
curl -X PUT http://localhost/api/tasks/<TASK_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>" \
     -H "Content-Type: application/json" \
     -d '{
           "status": "done"
         }'
```

### 13. Delete a Task (requires token)

Replace `<TASK_ID>` with the actual task ID.

```bash
curl -X DELETE http://localhost/api/tasks/<TASK_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 14. Create a Comment on a Task (requires token)

Replace `<TASK_ID>` with the actual task ID.

```bash
curl -X POST http://localhost/api/tasks/<TASK_ID>/comments \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>" \
     -H "Content-Type: application/json" \
     -d '{
           "content": "Great progress on this task!"
         }'
# Note the 'id' of the created comment for later requests.
```

### 15. Get Comments for a Task (requires token)

Replace `<TASK_ID>` with the actual task ID.

```bash
curl -X GET http://localhost/api/tasks/<TASK_ID>/comments \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 16. Update a Comment (requires token)

Replace `<COMMENT_ID>` with the actual comment ID.

```bash
curl -X PUT http://localhost/api/comments/<COMMENT_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>" \
     -H "Content-Type: application/json" \
     -d '{
           "content": "Updated comment: Excellent progress!"
         }'
```

### 17. Delete a Comment (requires token)

Replace `<COMMENT_ID>` with the actual comment ID.

```bash
curl -X DELETE http://localhost/api/comments/<COMMENT_ID> \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 18. Get Unseen Notifications (requires token)

```bash
curl -X GET http://localhost/api/notifications \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

### 19. Mark a Notification as Read (requires token)

Replace `<NOTIFICATION_ID>` with the actual notification ID (UUID).

```bash
curl -X POST http://localhost/api/notifications/<NOTIFICATION_ID>/mark-as-read \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
```

## ⚠️ Things I would do differently with more time

*   **Comprehensive Testing**: Implement unit tests for all services and repositories, and integration tests for all API endpoints to achieve the 70% test coverage requirement.
*   **CI/CD Pipeline**: Set up a basic CI pipeline (e.g., GitHub Actions) to run tests, static analysis (PHPStan), and linting (PHP_CodeSniffer/Laravel Pint) automatically on pushes.
*   **More Robust Caching**: Explore more granular cache invalidation strategies, potentially using cache tags or event listeners for more precise control.
*   **Notification Channels**: Implement additional notification channels (e.g., email, Slack) for the `TaskUpdated` notification.
*   **User Assignment to Tasks**: Add functionality to assign tasks to specific users, triggering notifications for the assignee.
*   **Soft Deletes**: Implement soft deletes for models (Projects, Tasks, Comments) instead of permanent deletion.
*   **Resource Filtering/Sorting**: Enhance filtering and sorting capabilities for all resources, not just tasks.
*   **API Versioning**: Implement API versioning (e.g., `/api/v1/projects`) for future scalability.
*   **Docker Optimization**: Optimize the Docker setup for production, including multi-stage builds and smaller base images.
*   **Documentation Generation**: Use tools like OpenAPI/Swagger to generate interactive API documentation.
*   **Error Handling Refinement**: Create custom exception classes for specific business logic errors and map them to appropriate HTTP responses.
*   **Frontend Integration**: Develop a simple frontend to demonstrate API usage.
