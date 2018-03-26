<?php

use App\Weekday;
use Illuminate\Database\Seeder;

class WeekdaySeeder extends Seeder {
    
	public function run() {
		$weekdays = [
			[
				'name' => 'Mon',
				'title' => '星期一',
				'value' => 1

			],
			[
				'name' => 'Tue',
				'title' => '星期二',
				'value' => 2

			],
			[
				'name' => 'Wed',
				'title' => '星期三',
				'value' => 3
			],
			[
				'name' => 'Thu',
				'title' => '星期四',
				'value' => 4
			],
			
			[
				'name' => 'Fri',
				'title' => '星期五',
				'value' => 5
			],
			[
				'name' => 'Sat',
				'title' => '星期六',
				'value' => 6
			],

		];

		foreach ($weekdays as $key => $value) {
			Weekday::create($value);
		}
	}
}
