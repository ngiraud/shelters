<?php

namespace App\Actions\Shelter;

use App\Exceptions\StoreShelterException;
use App\Models\Shelter;

class StoreShelterAction
{
    protected ?Shelter $shelter = null;

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): Shelter
    {
        throw_if(is_null($this->shelter), StoreShelterException::missingShelter());

        $this->shelter->fill($data)->save();

        return $this->shelter;
    }

    public function onShelter(Shelter $shelter): static
    {
        $this->shelter = $shelter;

        return $this;
    }
}
