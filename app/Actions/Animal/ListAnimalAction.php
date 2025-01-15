<?php

declare(strict_types=1);

namespace App\Actions\Animal;

use App\Models\Animal;
use App\Models\Shelter;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Pipeline;

class ListAnimalAction
{
    protected Shelter $shelter;

    /**
     * @var array<string,mixed>
     */
    protected array $data = [];

    /**
     * @param  array<string,mixed>  $data
     *
     * @return Paginator<Animal>
     */
    public function handle(Shelter $shelter, array $data = []): Paginator
    {
        $this->shelter = $shelter;
        $this->data = $data;

        return Pipeline::send($this->shelter->animals())
                       ->through([
                           $this->pushRelations(...),
                           $this->sort(...),
                           $this->applyFilters(...),
                       ])
                       ->then($this->paginate(...));
    }

    /**
     * @return Paginator<Animal>
     */
    protected function pushRelations(Builder $query, Closure $next): Paginator
    {
        $query->with(['shelter', 'species']);

        return $next($query);
    }

    /**
     * @return Paginator<Animal>
     */
    protected function sort(Builder $query, Closure $next): Paginator
    {
        $query->latest('created_at');

        return $next($query);
    }

    /**
     * @return Paginator<Animal>
     */
    protected function applyFilters(Builder $query, Closure $next): Paginator
    {
        $query
            ->when(
                !empty($this->data['search']),
                fn(Builder $query) => $query->whereLike('name', "%{$this->data['search']}%")
            )
            ->when(
                !empty($this->data['species']),
                fn(Builder $query) => $query->where('species_id', $this->data['species'])
            )
            ->when(
                !empty($this->data['gender']),
                fn(Builder $query) => $query->where('gender', $this->data['gender'])
            );

        return $next($query);
    }

    /**
     * @return Paginator<Animal>
     */
    protected function paginate(Builder $query): Paginator
    {
        return $query->paginate()->withQueryString();
    }
}
