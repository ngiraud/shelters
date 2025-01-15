<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpeciesRequest;
use App\Http\Requests\UpdateSpeciesRequest;
use App\Http\Resources\SpeciesResource;
use App\Models\Species;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SpeciesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the species.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<SpeciesResource>>
     *
     * @return AnonymousResourceCollection<LengthAwarePaginator<SpeciesResource>>
     */
    public function index(): AnonymousResourceCollection
    {
        return SpeciesResource::collection(
            Species::orderby('name')->paginate()
        );
    }

    /**
     * Store a newly created species.
     */
    public function store(StoreSpeciesRequest $request): SpeciesResource
    {
        $this->authorize('create', Species::class);

        $species = Species::create($request->validated());

        return SpeciesResource::make($species);
    }

    /**
     * Display the specified species.
     */
    public function show(Species $species): SpeciesResource
    {
        return SpeciesResource::make($species);
    }

    /**
     * Update the specified species.
     */
    public function update(UpdateSpeciesRequest $request, Species $species): SpeciesResource
    {
        $species->update($request->validated());

        return SpeciesResource::make($species);
    }

    /**
     * Remove the specified species.
     */
    public function destroy(Species $species): Response
    {
        $species->animals()->update([
            'species_id' => Species::where('name', 'Uncategorized')->value('id'),
        ]);

        $species->delete();

        return response()->noContent();
    }
}
