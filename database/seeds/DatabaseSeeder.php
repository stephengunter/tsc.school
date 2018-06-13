<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        $this->call(WeekdaySeeder::class);
        $this->call(PaywaySeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(AreaSeeder::class);
		$this->call(CityDistrictSeeder::class);
        $this->call(UserSeeder::class);
        
        $this->call(IdentitiesSeeder::class);
        $this->call(DiscountSeeder::class);

        $this->call(WagesSeeder::class);

        $this->call(ContentSeeder::class);
        
		
    }
}
