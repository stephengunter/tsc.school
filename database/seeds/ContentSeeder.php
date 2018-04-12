<?php

use App\Content;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder {
    
	public function run() {
        $path='app/templates/';
     
		$contents = [
			[
				'key' => 'about',
				'title'=> '單位理念',
				'content'=> \File::get(storage_path($path . '單位理念.html')),
				'active' => 1,
                'importance' => 30,
                'reviewed' => true
                
			],
			[
				'key' => 'about',
				'title'=> '校本部簡介',
				'content'=> \File::get(storage_path($path . '校本部簡介.html')),
				'active' => 1,
				'importance' => 25,
                'reviewed' => true
			],
			[
				'key' => 'about',
				'title'=> '設置辦法',
				'content'=> \File::get(storage_path($path . '設置辦法.html')),
				'active' => 1,
				'importance' => 22,
                'reviewed' => true
			],

			[
				'key' => 'faq',
				'title'=> '報名須知',
				'content'=> \File::get(storage_path($path . '報名須知.html')),
				'active' => 1,
				'importance' => 30,
                'reviewed' => true
			],
			
           

		];
  
		foreach ($contents as $key => $value) {
			Content::create($value);
		}
	}
}
