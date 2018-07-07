<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Core\Centers;

class Admin extends Model
{
    use Centers;
    
    protected $primaryKey = 'userId';

    protected $fillable = [ 'active', 'removed', 'updatedBy','ps'];
       
    public static function init()
	{
		return [
			'active' => 1,
            'removed' => 0,
            'ps' => ''

		];
	}  
     
    public function user()
    {
        return $this->belongsTo('App\User','userId');
    }

    public function centers()
    {
        return $this->belongsToMany(Center::class,'center_admin','admin_id','center_id');
    }

    public function isHeadCenterAdmin()
    {
        $centers=$this->centers;
        if(!count($centers)) return false;

        $headCenter=$centers->where('head')->first();
        if(!$headCenter) return false;

        return true;


    }

    public function centersCanAdmin()
    {
        $centers=$this->centers;
        
        if(!count($centers)) return [];

        $headCenter=$centers->where('head',true)->first();
        
        if(!$headCenter) return $centers;

        return Center::where('removed',false)->get();
      
    }

    public function setRole($roleName)
    {
        if($roleName==Role::bossRoleName()){
            $this->user->removeRole(Role::staffRoleName());
            $this->user->addRole(Role::bossRoleName());
        }else  if($roleName==Role::staffRoleName()){
            $this->user->removeRole(Role::bossRoleName());
            $this->user->addRole(Role::staffRoleName());
        }

        
    }
    
    public function addToCenter(Center $center)
	{
        if($this->inCenter($center)) return;
		$this->centers()->attach($center->id);
    }

}
