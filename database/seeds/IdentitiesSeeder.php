<?php

use App\Identity;
use Illuminate\Database\Seeder;

class IdentitiesSeeder extends Seeder {
    
	public function run() {

		$identities = [
			[
                'name' => '舊生',
                'code' => 'again',
                'member' => false,

			],
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
				'name' => '65歲以上銀髮族',
                'code' => 'over65',
                'member' => false,
			],
			
			[
                'name' => '75歲以上銀髮族',
                'code' => 'over75',
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

        ];

        $memberIdentities = [
			[
                'name' => '慈濟志業體同仁',
                'code' => 'staff',
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
        
        foreach ($memberIdentities as $identity) {
			Identity::create($identity);
		}
	}
}
