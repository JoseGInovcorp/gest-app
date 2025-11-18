<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Se o valor for null ou vazio, retornar como está
        if ($value === null || $value === '') {
            return $value;
        }

        // Decifrar o valor sem desserializar
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Se falhar a decifrar, retornar o valor original
            // (pode ser um valor que ainda não foi cifrado)
            return $value;
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Se o valor for null ou vazio, retornar como está
        if ($value === null || $value === '') {
            return $value;
        }

        // Cifrar o valor sem serializar
        return Crypt::encryptString($value);
    }
}
