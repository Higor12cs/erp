<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\Sequential;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use BelongsToTenant, HasUuids, Sequential;

    protected $fillable = [
        'tenant_id',
        'sequential_id',
        'first_name',
        'last_name',
        'legal_name',
        'cpf_cnpj',
        'rg',
        'ie',
        'birth_date',
        'email',
        'phone',
        'whatsapp',
        'zip_code',
        'address',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'active',
        'created_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
