<?php

use App\Wage;
use Illuminate\Database\Seeder;

class WagesSeeder extends Seeder {
    
	public function run() {

		$wages = [
			[
				'name' => '一般講師',
				'small_day' => 500,
				'small_night' => 600,
				'small_holiday' => 600,

				'big_day' => 700,
				'big_night' => 800,
				'big_holiday' => 800,

				'lecture' => 1000

			],
			[
				'name' => '講師',
				'small_day' => 575,
				'small_night' => 615,
				'small_holiday' => 615,

				'big_day' => 700,
				'big_night' => 800,
				'big_holiday' => 800,

				'lecture' => 1300
			],
			[
                'name' => '助理教授',
				'small_day' => 630,
				'small_night' => 665,
				'small_holiday' => 665,

				'big_day' => 700,
				'big_night' => 800,
				'big_holiday' => 800,

				'lecture' => 1500
			],
			[
				'name' => '副教授',
				'small_day' => 685,
				'small_night' => 710,
				'small_holiday' => 710,

				'big_day' => 700,
				'big_night' => 800,
				'big_holiday' => 800,

				'lecture' => 1600
			],
			
			[
				'name' => '教授',
				'small_day' => 795,
				'small_night' => 830,
				'small_holiday' => 830,

				'big_day' => 795,
				'big_night' => 830,
				'big_holiday' => 830,

				'lecture' => 1800
            ],
            [
				'name' => '外籍講師',
				'small_day' => 800,
				'small_night' => 800,
				'small_holiday' => 800,

				'big_day' => 800,
				'big_night' => 800,
				'big_holiday' => 800,

				
            ],
            [
				'name' => '特殊講師',
				'code' => 'special'
            ]

        ];

		foreach ($wages as $wage) {
			Wage::create($wage);
        }
        
       
	}
}
