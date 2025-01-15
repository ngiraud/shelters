<?php

namespace App\Actions\Animal;

use App\Exceptions\StoreAnimalException;
use App\Models\Animal;
use App\Models\Shelter;

class StoreAnimalAction
{
    protected ?Animal $animal = null;

    protected ?Shelter $shelter = null;

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): Animal
    {
        throw_if(is_null($this->shelter), StoreAnimalException::missingShelter());

        $animal = $this->animal ?? new Animal(['shelter_id' => $this->shelter->id]);

        $animal->fill($data)->save();

        return $animal;
    }

    public function onAnimal(Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function onShelter(Shelter $shelter): static
    {
        $this->shelter = $shelter;

        return $this;
    }
}
