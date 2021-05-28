<?php

namespace Database\Seeders;

use App\Models\Subscribers\Subscribe;
use Illuminate\Database\Seeder;

class SubscribersTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscribers = [
            [
                "name" => "Wahyu Aji Sulaiman",
                "address" => "3310", 
                "phone" => "+6285702151766"   
            ],
            [
                "name"  => "Arif Sony Wibowo",
                "address" => "3310", 
                "phone" => "+6285702151766"
            ]
        ];

        foreach($subscribers as $item) {
            $subscriber = Subscribe::where('phone', $item['phone'])->first();
            if(empty($subscriber)) {
                Subscribe::create([
                    'name'  => $item['name'],
                    'address' => $item['address'],
                    'phone' => $item['phone']
                ]);
            }
        }
    }
}
