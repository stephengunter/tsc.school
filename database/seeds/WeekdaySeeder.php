<?php

use App\Weekday;
use Illuminate\Database\Seeder;

class WeekdaySeeder extends Seeder {
    
	public function run() {
		$weekdays = [
			[
				'name' => 'Sun',
				'title' => '星期日',
				'val' => 0

			],
			[
				'name' => 'Mon',
				'title' => '星期一',
				'val' => 1

			],
			[
				'name' => 'Tue',
				'title' => '星期二',
				'val' => 2

			],
			[
				'name' => 'Wed',
				'title' => '星期三',
				'val' => 3
			],
			[
				'name' => 'Thu',
				'title' => '星期四',
				'val' => 4
			],
			
			[
				'name' => 'Fri',
				'title' => '星期五',
				'val' => 5
			],
			[
				'name' => 'Sat',
				'title' => '星期六',
				'val' => 6
			],

		];

		foreach ($weekdays as $key => $val) {
			Weekday::create($val);
		}
	}
}
