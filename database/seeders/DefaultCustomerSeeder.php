<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Tenant $tenant): void
    {
        $user = User::where('tenant_id', $tenant->id)->first();

        Customer::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Cliente Padrão',
            'created_by' => $user->id,
        ]);
    }
}
