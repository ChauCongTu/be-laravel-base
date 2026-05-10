<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Knowledge;

use App\Domains\Knowledge\Domain\Contracts\FolderRepositoryInterface;
use App\Domains\Knowledge\Domain\Folder\CreateFolderAction;
use App\Domains\Knowledge\Domain\Folder\DeleteFolderAction;
use App\Domains\Knowledge\Domain\Folder\UpdateFolderAction;
use App\Domains\Knowledge\Exceptions\FolderNotFoundException;
use App\Domains\Knowledge\Requests\CreateFolderRequest;
use App\Domains\Knowledge\Requests\UpdateFolderRequest;
use App\Domains\Knowledge\Resources\FolderResource;
use App\Domains\Shared\Exceptions\UnauthorizedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class FolderController
{
    public function __construct(
        private readonly FolderRepositoryInterface $repository,
        private readonly CreateFolderAction        $createAction,
        private readonly UpdateFolderAction        $updateAction,
        private readonly DeleteFolderAction        $deleteAction,
    ) {}

    /** GET /api/v1/folders */
    public function index(Request $request): AnonymousResourceCollection
    {
        $folders = $this->repository->listForUser($request->user()->id);

        return FolderResource::collection($folders);
    }

    /** POST /api/v1/folders */
    public function store(CreateFolderRequest $request): JsonResponse
    {
        $folder = $this->createAction->execute($request->toFolderData());

        return (new FolderResource($folder))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /** GET /api/v1/folders/{folder} */
    public function show(Request $request, int $folder): JsonResponse
    {
        $model = $this->repository->findWithRelations($folder);

        if (!$model) {
            return response()->json(['message' => "Folder with ID {$folder} not found."], Response::HTTP_NOT_FOUND);
        }

        if (!$model->canBeViewedBy($request->user()->id)) {
            return response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        return (new FolderResource($model))->response();
    }

    /** PUT /api/v1/folders/{folder} */
    public function update(UpdateFolderRequest $request, int $folder): JsonResponse
    {
        try {
            $model = $this->updateAction->execute($folder, $request->toFolderData());

            return (new FolderResource($model))->response();
        } catch (FolderNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }

    /** DELETE /api/v1/folders/{folder} */
    public function destroy(Request $request, int $folder): JsonResponse
    {
        try {
            $this->deleteAction->execute($folder, $request->user()->id);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (FolderNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
