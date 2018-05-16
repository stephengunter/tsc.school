
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
				
	}

	

}
	
