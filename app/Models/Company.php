<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'logo',
        'name',
        'address',
        'postal_code',
        'city',
        'nif',
    ];

    /**
     * Get the company instance (singleton pattern).
     * Se não existir, cria um registo vazio.
     */
    public static function getInstance()
    {
        $company = self::first();

        if (!$company) {
            $company = self::create([
                'name' => 'Gest-App',
                'nif' => null,
                'address' => null,
                'postal_code' => null,
                'city' => null,
                'logo' => null,
            ]);
        }

        return $company;
    }
}
