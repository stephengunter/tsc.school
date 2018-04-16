<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Content;
use App\Services\Contents;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class AboutController extends Controller
{
    
    public function __construct(Contents $contents)
    {
        $this->contents=$contents;
    }
    
    public function index()
    {
        $request=request();
        $id=0;
        if($request->id)  $id=(int)$request->id;

        $key = 'about';
		$contents = $this->contents->fetchContents($key);
        $contents =  $this->contents->getOrdered($contents)->get();

        $selectedContent=$contents->where('id',$id)->first();
        if(!$selectedContent) $selectedContent=$contents->first();
      

        $menus = $contents->map(function ($item) use($selectedContent){
            return [
                'value' => $item->id,
                'text' => $item->title,
                'active' => $item->id ==  $selectedContent->id,
                'url' => sprintf('/about?id=%d',  $item->id )
            ];
        });

      
        
        $model=[
            'title' => '關於我們',
            'topMenus' => $this->clientMenus(),
            'menus' => $menus,

            'model' => $selectedContent,
        ];

        

        return view('client.about')->with($model);
    }

   
}
