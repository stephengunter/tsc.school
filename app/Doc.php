<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Storage;

class Doc extends Model
{
    protected $fillable = [
        'path', 'type', 'name' , 'title', 'ps', 
        'width' ,'height','peviewPath', 'importance', 'updatedBy', 
    ];

    public static function init()
	{
		return [
			'path' => '',
            'type' => '',
            'name' => '',
            'title' => '',
            'ps'=> '',
            'importance' => 0,
        ];
        
    }

    public function  getStoragePath()
    {
        $storage = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
        $folder='downloads/';
       
        return $storage .$folder . $this->path ;
    }

    public function  getUrl()
    {
        return config('app.backend.url') . '/downloads/' . $this->id;
    }
}
