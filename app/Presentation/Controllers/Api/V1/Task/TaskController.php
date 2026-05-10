<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Task;

use App\Domains\Shared\Exceptions\UnauthorizedException;
use App\Domains\Task\Domain\Contracts\TaskRepositoryInterface;
use App\Domains\Task\Domain\CreateTaskAction;
use App\Domains\Task\Domain\DeleteTaskAction;
use App\Domains\Task\Domain\UpdateTaskAction;
use App\Domains\Task\Exceptions\TaskNotFoundException;
use App\Domains\Task\Requests\CreateTaskRequest;
use App\Domains\Task\Requests\UpdateTaskRequest;
use App\Domains\Task\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class TaskController
{
    public function __construct(
        private readonly TaskRepositoryInterface $repository,
        private readonly CreateTaskAction        $createAction,
        private readonly UpdateTaskAction        $updateAction,
        private readonly DeleteTaskAction        $deleteAction,
    ) {}

    /**
     * GET /api/v1/tasks
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<TaskResource>>
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->repository->paginateForUser(
            $request->user()->id,
            $request->only(['status', 'priority', 'overdue', 'due_today', 'due_soon']),
        );

        return TaskResource::collection($tasks);
    }

    /** POST /api/v1/tasks */
    public function store(CreateTaskRequest $request): JsonResponse
    {
        $task = $this->createAction->execute($request->toTaskData());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /** GET /api/v1/tasks/{task} */
    public function show(Request $request, int $task): JsonResponse
    {
        $model = $this->repository->findById($task);

        if (!$model) {
            return response()->json(['message' => "Task with ID {$task} not found."], Response::HTTP_NOT_FOUND);
        }

        if (!$model->canBeViewedBy($request->user()->id)) {
            return response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        return (new TaskResource($model))->response();
    }

    /** PUT /api/v1/tasks/{task} */
    public function update(UpdateTaskRequest $request, int $task): JsonResponse
    {
        try {
            $model = $this->updateAction->execute($task, $request->toTaskData());

            return (new TaskResource($model))->response();
        } catch (TaskNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }

    /** DELETE /api/v1/tasks/{task} */
    public function destroy(Request $request, int $task): JsonResponse
    {
        try {
            $this->deleteAction->execute($task, $request->user()->id);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (TaskNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
