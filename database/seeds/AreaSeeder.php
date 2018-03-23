<?php

use App\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder {
   

    public function run() 
    {
		$areas = [
			[
				'name' => '北北基',
				'code' => 'N',

			],
			[
				'name' => '桃竹苗',
				'code' => 'NW',

			],
			[
				'name' => '中彰投',
				'code' => 'C',
			],
			[
				'name' => '雲嘉南',
				'code' => 'CS',
			],
			
			[
				'name' => '高高屏',
				'code' => 'S',
			],

		];

		foreach ($areas as $key => $value) {
			Area::create($value);
		}
	}
}
