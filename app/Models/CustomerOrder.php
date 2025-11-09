<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'proposal_date',
        'validity_date',
        'customer_id',
        'status',
        'total_value',
        'notes',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'validity_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'customer_id');
    }

    /**
     * Get the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /**
     * Calculate and update the total value
     */
    public function calculateTotal(): void
    {
        $this->total_value = $this->items()->sum('total');
        $this->save();
    }

    /**
     * Generate the next order number
     */
    public static function generateNumber(): string
    {
        $year = date('Y');
        $prefix = "EC-$year-";

        $lastOrder = static::withTrashed()
            ->where('number', 'like', $prefix . '%')
            ->orderBy('number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
