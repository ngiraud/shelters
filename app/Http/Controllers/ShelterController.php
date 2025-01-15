<?php

namespace App\Http\Controllers;

use App\Actions\Shelter\StoreShelterAction;
use App\Http\Requests\StoreShelterRequest;
use App\Http\Requests\UpdateShelterRequest;
use App\Http\Resources\ShelterResource;
use App\Models\Shelter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ShelterController extends Controller
{
    /**
     * Display a listing of the shelters.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<ShelterResource>>
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<ShelterResource>>
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Shelter::class);

        return ShelterResource::collection(
            Shelter::orderBy('name')->paginate()
        );
    }

    /**
     * Store a newly created shelter.
     */
    public function store(StoreShelterRequest $request): Response
    {
        $this->authorize('create', Shelter::class);

        return response()->noContent();
    }

    /**
     * Display the specified shelter.
     */
    public function show(Shelter $shelter): ShelterResource
    {
        $this->authorize('view', $shelter);

        return ShelterResource::make($shelter);
    }

    /**
     * Update the specified shelter.
     */
    public function update(UpdateShelterRequest $request, Shelter $shelter, StoreShelterAction $action): ShelterResource
    {
        $this->authorize('update', $shelter);

        $shelter = $action
            ->onShelter($shelter)
            ->execute($request->validated());

        return ShelterResource::make($shelter);
    }

    /**
     * Remove the specified shelter.
     */
    public function destroy(Shelter $shelter): Response
    {
        $this->authorize('delete', $shelter);

        return response()->noContent();
    }
}
