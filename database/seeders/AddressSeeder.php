<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);
        $user->address()->create([
            'name' => 'adress1',
            'delivery_address' => 'İstanbul / Kadıköy',
            'bill_address1' => 'İstanbul / Kadıköy',
            'bill_address2' => 'İstanbul / Kadıköy',
            'bill_city' => 'İstanbul',
            'bill_postcode' => '3542',
            'bill_state' => 'Bağdat Caddesi',
            'bill_country' => 'Turkey',
            'bill_email' => 'example@email.com',
            'bill_phone' => '5370000000',
        ]);
    }
}
