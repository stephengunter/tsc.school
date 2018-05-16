<?php

use App\Identity;
use App\Discount;
use App\Center;

use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder {
    
    public static function seedDiscountCenters()
    {
        if(!$this->currentUserIsDev()) return;
        $eastCenter = Center::where('removed',false)->where('head',true)->first();
       
        $eastDiscountCodes=[
            "new" , "multi" , "member" , "lotus", "over65", "poor", "religion"
        ];
        foreach($eastDiscountCodes as $eastDiscountCode){
           
            $discount=\App\Discount::where('code' , $eastDiscountCode)->first();
           
            $eastCenter->discounts()->attach($discount->id);
        }


        $westDiscountCodes = [
            "one-west", "multi-west", "over65-west" ,"helf-west" ,"lotus-west"
        ];


        $westCenters =Center::where('removed',false)->where('head',false)->where('oversea',false)->get(); 
        foreach ($westCenters as  $westCenter)
        {
            foreach($westDiscountCodes as $westDiscountCode){
                $discount=\App\Discount::where('code' , $westDiscountCode)->first();
                $westCenter->discounts()->attach($discount->id);
            }
        }
    }



    function seedEastDiscount( )
    {
        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '報名一科',
				'code' => 'new',
                'min' => 1,
                'pointOne' => 90,
                'pointTwo' => 100,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])

        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '同時報名兩科以上',
				'code' => 'multi',
                'min' => 2,
                'pointOne' => 85,
                'pointTwo' => 100,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])

        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '各級學校在校生、慈濟志業體同仁、慈誠委員、榮譽董事',
				'code' => 'member',
                'min' => 1,
                'pointOne' => 85,
                'pointTwo' => 100,
                'prove' => false,
                'ps' => '',
                'active' => true
            ]),
            [ "student","staff","committee","director"]

        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '持中國信託蓮花卡刷卡繳費',
				'code' => 'lotus',
                'min' => 1,
                'pointOne' => 90,
                'pointTwo' => 90,
                'prove' => false,
                'ps' => '(本人)',
                'active' => true
            ])
        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '銀髮族65歲以上',
				'code' => 'over65',
                'min' => 1,
                'age' => 65,
                'pointOne' => 80,
                'pointTwo' => 80,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])
        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '身心障礙朋友',
				'code' => 'disabled',
                'min' => 1,
                'pointOne' => 80,
                'pointTwo' => 80,
                'prove' => true,
                'ps' => '(須提供證明)',
                'active' => true
            ]),
            ["disability"]
        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '低收入戶',
				'code' => 'poor',
                'min' => 1,
                'pointOne' => 50,
                'pointTwo' => 50,
                'prove' => true,
                'ps' => '(須提供證明)',
                'active' => true
            ]),
            ["poor"]
        );

        $this->createDiscount(
            new Discount([
                'key' => 'east',
                'name' => '宗教師',
				'code' => 'religion',
                'min' => 1,
                'pointOne' => 50,
                'pointTwo' => 50,
                'prove' => true,
                'ps' => '(須提供證明)',
                'active' => true
            ]),
            ["religion"]
        );


    }

    function seedWestDiscount( )
    {
        $this->createDiscount(
            new Discount([
                'key' => 'west',
                'name' => '報名一科',
				'code' => "one-west",
                'min' => 1,
                'pointOne' => 85,
                'pointTwo' => 100,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])

        );

        $this->createDiscount(
            new Discount([
                'key' => 'west',
                'name' =>  '同時報名兩科以上',
				'code' => 'multi-west',
                'min' => 2,
                'pointOne' => 80,
                'pointTwo' => 100,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])

        );

        $this->createDiscount(
            new Discount([
                'key' => 'west',
                'name' => '年滿65歲',
				'code' => "over65-west",
                'min' => 1,
                'age' => 65,
                'pointOne' => 70,
                'pointTwo' => 70,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])

        );

        $this->createDiscount(
            new Discount([
                'key' => 'west',
                'name' => '年滿75歲',
				'code' => "over75-west",
                'min' => 1,
                'age' => 75,
                'pointOne' => 50,
                'pointTwo' => 50,
                'prove' => false,
                'ps' => '',
                'active' => true
            ])

        );


        $this->createDiscount(
            new Discount([
                'key' => 'west',
                'name' => '身心障礙、低收入戶、宗教師',
				'code' =>  "helf-west",
                'min' => 1,
                'pointOne' => 50,
                'pointTwo' => 50,
                'prove' => true,
                'ps' => '(須提供證明)',
                'active' => true
            ]),
            ["disability" , "poor", "religion"]
        );

        $this->createDiscount(
            new Discount([
                'key' => 'west',
                'name' => '持中國信託蓮花卡刷卡繳費',
				'code' => 'lotus-west',
                'min' => 1,
                'pointOne' => 90,
                'pointTwo' => 90,
                'prove' => false,
                'ps' => '(本人)',
                'active' => true
            ])
        );


    }

    function createDiscount(Discount $discount, array $identities=[])
    {
        $exist=Discount::where('code' , $discount->code )->first();
        if($exist) return;

        $discount->save();
        if(!count($identities)) return;

        foreach($identities as $code){
            $identity=Identity::where('code' , $code)->first();

            $discount->identities()->attach($identity->id);
            
        }

    }

    

    public function run() 
    {
        $this->seedEastDiscount();
        $this->seedWestDiscount();
		
	}
}
