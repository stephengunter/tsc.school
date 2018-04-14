<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder {
    
	public function run() {
		$role = [
			[
				'name' => 'Dev',
				'title' => '開發者',

			],
			[
				'name' => 'Boss',
				'title' => '主管',

			],
			[
				'name' => 'Staff',
				'title' => '職員',
			],
			[
				'name' => 'Teacher',
				'title' => '教師',
			],
			
			[
				'name' => 'Student',
				'title' => '學生',
			],

			[
				'name' => 'Volunteer',
				'title' => '志工',
			],

		];

		foreach ($role as $key => $value) {
			Role::create($value);
		}
	}
}
