<?php

use App\Identity;
use Illuminate\Database\Seeder;

class IdentitiesSeeder extends Seeder {
    
	public function run() {
        $identities = [
			
			[
				'name' => '社區志工',
                'member' => false,

            ],
        ];
		$discountIdentities = [
			
			[
				'name' => '各級學校在校生',
                'code' => 'student',
                'member' => false,

			],
			[
                'name' => '原住民',
                'code' => 'amei',
                'member' => false,
			],
            [
                'name' => '身心障礙朋友',
                'code' => 'disability',
                'member' => false,
            ],
            [
                'name' => '低收入戶',
                'code' => 'poor',
                'member' => false,
            ],
            [
                'name' => '宗教師',
                'code' => 'religion',
                'member' => false,
			],
            [
                'name' => '慈濟志業體同仁',
                'code' => 'staff',
                'member' => false,

			],
        ];

        $memberIdentities = [
			[
				'name' => '會員',
                'code' => 'member',
                'member' => true,

			],
			[
				'name' => '慈誠委員',
                'code' => 'committee',
                'member' => true,

			],
			[
                'name' => '慈濟榮譽董事',
                'code' => 'director',
                'member' => true,
			]

        ];
        
        foreach ($identities as $identity) {
			Identity::create($identity);
        }
		foreach ($discountIdentities as $identity) {
			Identity::create($identity);
        }
        
        foreach ($memberIdentities as $identity) {
			Identity::create($identity);
		}
	}
}
