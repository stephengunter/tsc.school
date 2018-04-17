<?php

use App\Payway;
use Illuminate\Database\Seeder;

class PaywaySeeder extends Seeder {
    
	public function run() {

		$payways = [
			[
				'name' => '現金',
				'need_account' => true,
				'pay' => true,
				'back' => false,
				'auto' => false,
			],
			[
				'name' => '信用卡(現場)',
				'need_account' => false,
				'pay' => true,
				'back' => true,
				'auto' => true,
				'fee' => 2,
				'fee_percents' => true
			],
			[
				'name' => '信用卡(網路)',
				'need_account' => false,
				'pay' => true,
				'back' => true,
				'auto' => true,
				'fee' => 2.5,
				'fee_percents' => true
			],
			[
				'name' => '便利商店',
				'need_account' => true,
				'pay' => true,
				'back' => false,
				'auto' => true,
			],
			[
				'name' => '匯款',
				'need_account' => true,
				'pay' => true,
				'back' => true,
				'auto' => true,
				'fee' => 20,
				'fee_percents' => false
			]

        ];

        
        
        foreach ($payways as $payway) {
			Payway::create($payway);
		}
	}
}
