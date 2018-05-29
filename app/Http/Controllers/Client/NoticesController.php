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

        $pageSize=10;

        $notices = $this->notices->fetchNotices();
        
        $pageList = new PagedList($notices,$page,$pageSize);

        if($this->isAjaxRequest()){
          
            return response()->json($pageList);
        }
        
        $model=[
            'title' => '公告訊息',
            'topMenus' => $this->clientMenus(),
            
           
            'list' => $pageList,
        ];

        

        return view('client.notices.index')->with($model);
    }

    public function show($id)
    {
        $notice = Notice::findOrFail($id);
        $notice->date=new Carbon($notice->created_at);//->toDateString();
        $notice->date=$notice->date->toDateString();
        $model=[
            'title' => '公告訊息',
            'topMenus' => $this->clientMenus(),

            'notice' => $notice           
        ];

        return view('client.notices.details')->with($model);
        
    }
   
}
