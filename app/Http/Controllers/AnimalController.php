<?php

namespace App\Http\Controllers;

use App\Actions\Animal\ListAnimalAction;
use App\Actions\Animal\StoreAnimalAction;
use App\Http\Requests\IndexAnimalRequest;
use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AnimalController extends Controller
{
    /**
     * Display a listing of animals.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<AnimalResource>>
     *
     * @return InertiaResponse|AnonymousResourceCollection<LengthAwarePaginator<AnimalResource>>
     */
    public function index(IndexAnimalRequest $request, ListAnimalAction $action): InertiaResponse|AnonymousResourceCollection
    {
        if ($request->inertia() || !$request->wantsJson()) {
            return Inertia::render('Animal/Index');
        }

        return AnimalResource::collection(
            $action->handle(auth()->user()->shelter, $request->validated())
        );
    }

    public function create(): InertiaResponse
    {
        $this->authorize('create', Animal::class);

        return Inertia::render('Animal/Create');
    }

    /**
     * Store a newly created animal.
     */
    public function store(StoreAnimalRequest $request, StoreAnimalAction $action): AnimalResource
    {
        $this->authorize('create', Animal::class);

        $animal = $action
            ->onShelter($request->user()->shelter)
            ->execute($request->validated());

        return AnimalResource::make($animal);
    }

    /**
     * Display the specified animal.
     */
    public function show(Animal $animal): AnimalResource
    {
        $this->authorize('view', $animal);

        $animal->loadMissing(['shelter', 'species']);

        return AnimalResource::make($animal);
    }

    /**
     * Display the specified animal.
     */
    public function edit(Animal $animal): InertiaResponse
    {
        $this->authorize('update', $animal);

        return Inertia::render('Animal/Edit', [
            'animal' => AnimalResource::make($animal),
        ]);
    }

    /**
     * Update the specified animal.
     */
    public function update(UpdateAnimalRequest $request, Animal $animal, StoreAnimalAction $action): AnimalResource
    {
        $this->authorize('update', $animal);

        $animal = $action
            ->onShelter($request->user()->shelter)
            ->onAnimal($animal)
            ->execute($request->validated());

        return AnimalResource::make($animal);
    }

    /**
     * Remove the specified animal.
     */
    public function destroy(Animal $animal): Response
    {
        $this->authorize('delete', $animal);

        $animal->delete();

        return response()->noContent();
    }
}
