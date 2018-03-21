<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private function addDev(){
		$dev=config('app.dev');
		$name=$dev['name'];
		$email=$dev['email'];
		$phone=$dev['phone'];
		$user=\App\User::create([
			'name' => $name,
			'email' =>$email,
			'phone' => $phone,
			'password' => 'secret',
			'emailConfirmed' => true,
		
		]);
		$profile=new \App\Profile([
			'userId' => $user->id,					
			'fullname'=> '何金水',
			'dob' =>'1979-3-12',
			'gender' => 1,
		]);
		$user->profile()->save($profile);

		$roleName='Dev';
		$user->addRole($roleName);
	}
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(CityDistrictSeeder::class);
    }
}
