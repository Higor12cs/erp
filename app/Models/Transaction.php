<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Sequential;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use BelongsToTenant, HasUuids, Sequential;

    protected $fillable = [
        'tenant_id',
        'sequential_id',
        'account_id',
        'receivable_id',
        'payable_id',
        'type',
        'amount',
        'transaction_date',
        'description',
        'reconciled',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }

    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
