<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'vehicle_id', 'type', 'invoice_number', 'status', 'notes'];

    // ── Relationships ──

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function allocations()
    {
        return $this->hasManyThrough(StockAllocation::class, TransactionItem::class);
    }

    // ── Computed Attributes ──

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->items()->sum('total_price');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - $this->total_paid;
    }

    public function getGlassCostAttribute(): float
    {
        $allocations = StockAllocation::whereIn(
            'transaction_item_id',
            $this->items()->where('itemable_type', GlassProduct::class)->pluck('id')
        )->with('lot')->get();

        return (float) $allocations->sum(fn ($a) => $a->lot->purchase_cost * $a->quantity);
    }

    public function getProfitAttribute(): float
    {
        return $this->total_amount - $this->glass_cost;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->total_paid >= $this->total_amount && $this->total_amount > 0;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            TransactionStatus::Pending->value => 'amber',
            TransactionStatus::Confirmed->value => 'green',
            TransactionStatus::Cancelled->value => 'red',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return TransactionType::tryFrom($this->type)?->label() ?? $this->type;
    }

    // ── Query Scopes ──

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('type', $type) : $query;
    }

    // ── Helpers ──

    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $last = static::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderByDesc('invoice_number')
            ->first();

        if ($last) {
            $seq = (int) substr($last->invoice_number, -4) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('INV-%s-%04d', $year, $seq);
    }
}
