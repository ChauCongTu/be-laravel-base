<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Knowledge;

use App\Domains\Knowledge\Domain\Contracts\SnippetRepositoryInterface;
use App\Domains\Knowledge\Domain\Snippet\CreateSnippetAction;
use App\Domains\Knowledge\Domain\Snippet\DeleteSnippetAction;
use App\Domains\Knowledge\Domain\Snippet\UpdateSnippetAction;
use App\Domains\Knowledge\Exceptions\SnippetNotFoundException;
use App\Domains\Knowledge\Requests\CreateSnippetRequest;
use App\Domains\Knowledge\Requests\UpdateSnippetRequest;
use App\Domains\Knowledge\Resources\SnippetResource;
use App\Domains\Shared\Exceptions\UnauthorizedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class SnippetController
{
    public function __construct(
        private readonly SnippetRepositoryInterface $repository,
        private readonly CreateSnippetAction        $createAction,
        private readonly UpdateSnippetAction        $updateAction,
        private readonly DeleteSnippetAction        $deleteAction,
    ) {}

    /**
     * GET /api/v1/snippets
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<SnippetResource>>
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $snippets = $this->repository->paginateForUser(
            $request->user()->id,
            $request->only(['folder_id', 'language', 'search']),
        );

        return SnippetResource::collection($snippets);
    }

    /** POST /api/v1/snippets */
    public function store(CreateSnippetRequest $request): JsonResponse
    {
        $snippet = $this->createAction->execute($request->toSnippetData());

        return (new SnippetResource($snippet))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /** GET /api/v1/snippets/{snippet} */
    public function show(Request $request, int $snippet): JsonResponse
    {
        $model = $this->repository->findById($snippet);

        if (!$model) {
            return response()->json(['message' => "Snippet with ID {$snippet} not found."], Response::HTTP_NOT_FOUND);
        }

        if (!$model->canBeViewedBy($request->user()->id)) {
            return response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        return (new SnippetResource($model))->response();
    }

    /** PUT /api/v1/snippets/{snippet} */
    public function update(UpdateSnippetRequest $request, int $snippet): JsonResponse
    {
        try {
            $model = $this->updateAction->execute($snippet, $request->toSnippetData());

            return (new SnippetResource($model))->response();
        } catch (SnippetNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }

    /** DELETE /api/v1/snippets/{snippet} */
    public function destroy(Request $request, int $snippet): JsonResponse
    {
        try {
            $this->deleteAction->execute($snippet, $request->user()->id);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (SnippetNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
