<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1\Knowledge;

use App\Domains\Knowledge\Domain\Contracts\NoteRepositoryInterface;
use App\Domains\Knowledge\Domain\Note\CreateNoteAction;
use App\Domains\Knowledge\Domain\Note\DeleteNoteAction;
use App\Domains\Knowledge\Domain\Note\UpdateNoteAction;
use App\Domains\Knowledge\Exceptions\NoteNotFoundException;
use App\Domains\Knowledge\Requests\CreateNoteRequest;
use App\Domains\Knowledge\Requests\UpdateNoteRequest;
use App\Domains\Knowledge\Resources\NoteResource;
use App\Domains\Shared\Exceptions\UnauthorizedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class NoteController
{
    public function __construct(
        private readonly NoteRepositoryInterface $repository,
        private readonly CreateNoteAction        $createAction,
        private readonly UpdateNoteAction        $updateAction,
        private readonly DeleteNoteAction        $deleteAction,
    ) {}

    /**
     * GET /api/v1/notes
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<NoteResource>>
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $notes = $this->repository->paginateForUser(
            $request->user()->id,
            $request->only(['folder_id', 'is_pinned', 'type', 'search']),
        );

        return NoteResource::collection($notes);
    }

    /** POST /api/v1/notes */
    public function store(CreateNoteRequest $request): JsonResponse
    {
        $note = $this->createAction->execute($request->toNoteData());

        return (new NoteResource($note))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /** GET /api/v1/notes/{note} */
    public function show(Request $request, int $note): JsonResponse
    {
        $model = $this->repository->findById($note);

        if (!$model) {
            return response()->json(['message' => "Note with ID {$note} not found."], Response::HTTP_NOT_FOUND);
        }

        if (!$model->canBeViewedBy($request->user()->id)) {
            return response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        return (new NoteResource($model))->response();
    }

    /** PUT /api/v1/notes/{note} */
    public function update(UpdateNoteRequest $request, int $note): JsonResponse
    {
        try {
            $model = $this->updateAction->execute($note, $request->toNoteData());

            return (new NoteResource($model))->response();
        } catch (NoteNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }

    /** DELETE /api/v1/notes/{note} */
    public function destroy(Request $request, int $note): JsonResponse
    {
        try {
            $this->deleteAction->execute($note, $request->user()->id);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (NoteNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
