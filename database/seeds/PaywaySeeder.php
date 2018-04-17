<?php

use App\Payway;
use Illuminate\Database\Seeder;

class PaywaySeeder extends Seeder {
    
	public function run() {

		$payways = [
			[
                'name' => '現金',
			],
			[
				'name' => '信用卡',
			],
			[
                'name' => '便利商店',
			],
			[
				'name' => '匯款',
			]

        ];

        
        
        foreach ($payways as $payway) {
			Payway::create($payway);
		}
	}
}
