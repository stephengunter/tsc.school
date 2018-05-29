<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Notice;
use App\Services\Notices;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class NoticesController extends Controller
{
    
    public function __construct(Notices $notices)
    {
        $this->notices=$notices;
    }
    
    public function index()
    {
        $request=request();

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=999;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;

        $notices = $this->notices->fetchNotices();
        
        $pageList = new PagedList($notices,$page,$pageSize);

      
      
        // foreach($pageList->viewList as $signup){
        //     $signup->loadViewModel();
          
        //     if($canQuit)  $signup->canQuit=$this->canQuit($signup);
            
        // }  
        
        $model=[
            'title' => '公告訊息',
            'topMenus' => $this->clientMenus(),
            

            'list' => $pageList,
        ];

        

        return view('client.notices.index')->with($model);
    }

   
}
