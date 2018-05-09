
<?php


use Faker\Factory;
use Illuminate\Database\Seeder;
use App\Services\Users;
use App\User;
use App\Role;
use App\Profile;
use App\Address;
use App\ContactInfo;
use App\District;

class UserSeeder extends Seeder 
{
    public function __construct(Users $users)
    {
        $this->users=$users;
    }

    private function addDev(){
		$dev=config('app.dev');
		$fullname=$dev['fullname'];
		$email=$dev['email'];
		$phone=$dev['phone'];
		$user=new User([
			
			'email' =>$email,
			'phone' => $phone,
			'password' => 'secret',
			'emailConfirmed' => true,
		
		]);
		$profile=new Profile([
			'userId' => $user->id,					
			'fullname'=> $fullname,
			'sid' => 'F124597024',		
			'dob' =>'1979-3-12',
			'gender' => 1,
        ]);
        
        $user=$this->users->createUser($user,$profile);

        $this->users->addRole($user,Role::devRoleName());
        

        $name='test';
		$email='test@gmail.com';
		$phone='0932000000';
		$testUser=new User([
			
			'email' =>$email,
			'phone' => $phone,
			'password' => '000000',
			'emailConfirmed' => true,
		
		]);
		$testUserProfile=new Profile([
			'userId' => $testUser->id,	
			'sid' => 'A123456789',				
			'fullname'=> '何金銀',
			'dob' =>'1975-6-1',
			'gender' => 1,
        ]);
        
        $testUser=$this->users->createUser($testUser,$testUserProfile);

		$this->users->addRole($testUser,Role::devRoleName());
    }
    

	public function run()
	{
        $this->addDev();

        $roads=[ '中正','中華','民生','建國','忠孝', '仁愛','信義','和平'];
        
        $districts=District::all()->pluck('id')->toArray();

        $faker = Factory::create();
		
		foreach(range(1, 50) as $i) {
			$gender=( $i %2 == 0 );
			$sid= $faker->randomLetter();
			$sid .= $gender ? '1' : '2' ;
			$sid .= mt_rand(1, 99999999);
            $user = new User([
                
                'email' => $faker->unique()->safeEmail,
				'phone' =>  '093' . mt_rand(1, 9999999)
            ]);

            $profile=new Profile([
				'fullname'=> $faker->name,
				'sid' => $sid,
                'dob' => mt_rand(1945, 1995) . '-' .mt_rand(1, 12).'-'.mt_rand(1, 28),
                'gender' => ( $i %2 == 0 ),
            ]);

			$user=$this->users->createUser($user,$profile);
			
            
            $address=new Address([
                'districtId' => array_rand($districts, 1),
                'street' => $roads[array_rand($roads, 1)] . '路' . mt_rand(1, 300) . '號',
            ]);

            $contactInfo=new ContactInfo([
                'tel' => $faker->tollFreePhoneNumber     
            ]);

            $this->users->setContactInfo($user,$contactInfo,$address);
            
		}  
		


				
	}

	

}
	
