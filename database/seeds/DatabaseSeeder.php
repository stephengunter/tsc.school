<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(AreaSeeder::class);
		$this->call(CityDistrictSeeder::class);
		$this->call(UserSeeder::class);

		
    }
}
