<?php

use App\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder {
   

    public function run() 
    {
		$areas = [
			[
				'key' => 'east',
				'name' => '宜花東',
				'code' => 'E',

			],
			[
				'key' => 'west',
				'name' => '北北基',
				'code' => 'N',

			],
			[
				'key' => 'west',
				'name' => '桃竹苗',
				'code' => 'NW',

			],
			[
				'key' => 'west',
				'name' => '中彰投',
				'code' => 'C',
			],
			[
				'key' => 'west',
				'name' => '雲嘉南',
				'code' => 'CS',
			],
			
			[
				'key' => 'west',
				'name' => '高高屏',
				'code' => 'S',
			],

		];

		foreach ($areas as $key => $value) {
			Area::create($value);
		}
	}
}
