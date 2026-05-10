#!/bin/bash

# Laravel 12 DDD Structure Generator Script
# Personal LifeOS - Knowledge & Task Workspace

echo "Creating DDD directory structure for Laravel 12..."

# Domain Layer
echo "Creating Domain layer..."
mkdir -p app/Domain/Knowledge/Models
mkdir -p app/Domain/Knowledge/ValueObjects
mkdir -p app/Domain/Knowledge/DataTransferObjects
mkdir -p app/Domain/Knowledge/Events
mkdir -p app/Domain/Knowledge/Exceptions
mkdir -p app/Domain/Knowledge/Repositories

mkdir -p app/Domain/Task/Models
mkdir -p app/Domain/Task/ValueObjects
mkdir -p app/Domain/Task/DataTransferObjects
mkdir -p app/Domain/Task/Events
mkdir -p app/Domain/Task/Exceptions
mkdir -p app/Domain/Task/Repositories

# Application Layer
echo "Creating Application layer..."
mkdir -p app/Application/Knowledge/Actions
mkdir -p app/Application/Knowledge/Subscribers

mkdir -p app/Application/Task/Actions
mkdir -p app/Application/Task/Subscribers

# Presentation Layer
echo "Creating Presentation layer..."
mkdir -p app/Presentation/Api/V1/Controllers/Knowledge
mkdir -p app/Presentation/Api/V1/Controllers/Task
mkdir -p app/Presentation/Api/V1/Requests/Knowledge
mkdir -p app/Presentation/Api/V1/Requests/Task
mkdir -p app/Presentation/Api/V1/Resources/Knowledge
mkdir -p app/Presentation/Api/V1/Resources/Task
mkdir -p app/Presentation/Api/V2
mkdir -p app/Presentation/Web/Controllers

# Infrastructure Layer
echo "Creating Infrastructure layer..."
mkdir -p app/Infrastructure/Repositories/Knowledge
mkdir -p app/Infrastructure/Repositories/Task
mkdir -p app/Infrastructure/Services/AWS
mkdir -p app/Infrastructure/Services/External
mkdir -p app/Infrastructure/Migrations

# Create empty PHP files for Knowledge Domain
echo "Creating Knowledge Domain files..."
touch app/Domain/Knowledge/Models/Snippet.php
touch app/Domain/Knowledge/Models/Document.php
touch app/Domain/Knowledge/Models/Tag.php
touch app/Domain/Knowledge/ValueObjects/SnippetCode.php
touch app/Domain/Knowledge/ValueObjects/SnippetLanguage.php
touch app/Domain/Knowledge/ValueObjects/DocumentContent.php
touch app/Domain/Knowledge/DataTransferObjects/SnippetData.php
touch app/Domain/Knowledge/DataTransferObjects/DocumentData.php
touch app/Domain/Knowledge/DataTransferObjects/TagData.php
touch app/Domain/Knowledge/Events/SnippetCreated.php
touch app/Domain/Knowledge/Events/SnippetUpdated.php
touch app/Domain/Knowledge/Events/DocumentCreated.php
touch app/Domain/Knowledge/Exceptions/InvalidSnippetCodeException.php
touch app/Domain/Knowledge/Exceptions/SnippetNotFoundException.php
touch app/Domain/Knowledge/Repositories/SnippetRepositoryInterface.php
touch app/Domain/Knowledge/Repositories/DocumentRepositoryInterface.php

# Create empty PHP files for Task Domain
echo "Creating Task Domain files..."
touch app/Domain/Task/Models/Task.php
touch app/Domain/Task/Models/Category.php
touch app/Domain/Task/ValueObjects/TaskStatus.php
touch app/Domain/Task/ValueObjects/TaskPriority.php
touch app/Domain/Task/ValueObjects/CategoryName.php
touch app/Domain/Task/DataTransferObjects/TaskData.php
touch app/Domain/Task/DataTransferObjects/CategoryData.php
touch app/Domain/Task/Events/TaskCreated.php
touch app/Domain/Task/Events/TaskCompleted.php
touch app/Domain/Task/Events/CategoryCreated.php
touch app/Domain/Task/Exceptions/InvalidTaskStatusException.php
touch app/Domain/Task/Exceptions/TaskNotFoundException.php
touch app/Domain/Task/Repositories/TaskRepositoryInterface.php
touch app/Domain/Task/Repositories/CategoryRepositoryInterface.php

# Create empty PHP files for Application Layer
echo "Creating Application layer files..."
touch app/Application/Knowledge/Actions/CreateSnippetAction.php
touch app/Application/Knowledge/Actions/UpdateSnippetAction.php
touch app/Application/Knowledge/Actions/DeleteSnippetAction.php
touch app/Application/Knowledge/Actions/CreateDocumentAction.php
touch app/Application/Knowledge/Actions/GetSnippetAction.php
touch app/Application/Knowledge/Subscribers/SnippetCreatedSubscriber.php
touch app/Application/Knowledge/Subscribers/DocumentCreatedSubscriber.php

touch app/Application/Task/Actions/CreateTaskAction.php
touch app/Application/Task/Actions/UpdateTaskAction.php
touch app/Application/Task/Actions/CompleteTaskAction.php
touch app/Application/Task/Actions/CreateCategoryAction.php
touch app/Application/Task/Actions/GetTaskAction.php
touch app/Application/Task/Subscribers/TaskCreatedSubscriber.php
touch app/Application/Task/Subscribers/TaskCompletedSubscriber.php

# Create empty PHP files for Presentation Layer
echo "Creating Presentation layer files..."
touch app/Presentation/Api/V1/Controllers/Knowledge/SnippetController.php
touch app/Presentation/Api/V1/Controllers/Knowledge/DocumentController.php
touch app/Presentation/Api/V1/Controllers/Knowledge/TagController.php
touch app/Presentation/Api/V1/Controllers/Task/TaskController.php
touch app/Presentation/Api/V1/Controllers/Task/CategoryController.php

touch app/Presentation/Api/V1/Requests/Knowledge/CreateSnippetRequest.php
touch app/Presentation/Api/V1/Requests/Knowledge/UpdateSnippetRequest.php
touch app/Presentation/Api/V1/Requests/Knowledge/CreateDocumentRequest.php
touch app/Presentation/Api/V1/Requests/Task/CreateTaskRequest.php
touch app/Presentation/Api/V1/Requests/Task/UpdateTaskRequest.php
touch app/Presentation/Api/V1/Requests/Task/CreateCategoryRequest.php

touch app/Presentation/Api/V1/Resources/Knowledge/SnippetResource.php
touch app/Presentation/Api/V1/Resources/Knowledge/DocumentResource.php
touch app/Presentation/Api/V1/Resources/Knowledge/TagResource.php
touch app/Presentation/Api/V1/Resources/Task/TaskResource.php
touch app/Presentation/Api/V1/Resources/Task/CategoryResource.php

# Create empty PHP files for Infrastructure Layer
echo "Creating Infrastructure layer files..."
touch app/Infrastructure/Repositories/Knowledge/EloquentSnippetRepository.php
touch app/Infrastructure/Repositories/Knowledge/EloquentDocumentRepository.php
touch app/Infrastructure/Repositories/Task/EloquentTaskRepository.php
touch app/Infrastructure/Repositories/Task/EloquentCategoryRepository.php
touch app/Infrastructure/Services/AWS/S3Service.php
touch app/Infrastructure/Services/AWS/S3ServiceInterface.php

echo "DDD structure creation completed!"
echo "Total files created: $(find app -name '*.php' | wc -l)"
echo "Total directories created: $(find app -type d | wc -l)"
