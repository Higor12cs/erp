<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait Sequential
{
    public static function bootSequential()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $tenantId = Auth::user()->tenant_id;
                $entityType = $model->getTable();

                $model->sequential_id = self::getNextSequential($tenantId, $entityType);
            }
        });
    }

    public static function getNextSequential($tenantId, $entityType)
    {
        $sequence = DB::table('tenant_sequences')
            ->where('tenant_id', $tenantId)
            ->where('entity_type', $entityType)
            ->first();

        if (! $sequence) {
            DB::table('tenant_sequences')->insert([
                'tenant_id' => $tenantId,
                'entity_type' => $entityType,
                'last_sequence_value' => 1,
            ]);

            return 1;
        } else {
            $Sequential = $sequence->last_sequence_value + 1;
            DB::table('tenant_sequences')
                ->where('tenant_id', $tenantId)
                ->where('entity_type', $entityType)
                ->update(['last_sequence_value' => $Sequential]);

            return $Sequential;
        }
    }
}
