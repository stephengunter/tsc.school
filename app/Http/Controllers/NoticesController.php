<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\NoticeRequest;
use App\Notice;
use App\Admin;
use App\Center;
use App\Services\Notices;
use App\Services\Centers;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class NoticesController extends Controller
{
    public function __construct(Notices $notices,Centers $centers)
    {
        $this->notices=$notices;
        $this->centers=$centers;
    }

    
    function canEdit($notice=null)
    {

        if($this->currentUserIsDev()) return true;

        if(!$notice) return true;

        return $this->canAdminCenter($notice->center);
       
       
    }

    function canReview($notice=null)
    {
        if($this->currentUserIsDev()) return true;
        if(!$this->currentUser()->isBoss()) return false;

        return $this->canEdit($notice);

        
    }

    function seed()
    {
        $center=Center::where('code','A')->first();
        Notice::create([
            'date' => '2018-6-1',
            'centerId' =>$center->id,
            'title'=> '天然災害來襲時本學院停課之辦理依據',
            'content'=>'<p>各位推廣班程的學員們，您好：<br><br>有關颱風等天然災害來襲時，本學院相關課程停課與否，係依據所在主管機關（台北市政府）之宣佈上班上課與否而定。PMBA、PMLBA、PMBM學位學程之課程，係依據台北市或新竹縣政府任一方發佈停課公告而定。<br>若有下列情況之一，請學員隨時來電查詢或到本學院網址查看相關訊息、或請注意本學院email及簡訊發送。 <br>一、所屬機關學校僅宣佈各級學校停止上課：本學院維持正常上班，本學院班程停課，若屬企業班程，從企業決定。<br>二、所屬機關學校維持正常上班上課，但其他縣市政府宣佈停止上班上課、或鐵公路及空中交通停止發班，或有安全之虞等因素，導致授課教師可能無法來班上課、或考量學員上課往返之安全；屆時是否正常上課，仍請於本學院正常上班時間內來電查詢。若學員屬停班停課的縣市無法前來本部上課，則不計缺課。 <br>另，如遇有停課情況，該課程之節次順延或另擇適當時間實施補課，補課時間另行公布周知。 <br>提醒您做好防颱準備，並感謝您的配合。 <br>臺大進修推廣學院關心您！</p>',
            'reviewed' => 1,
            'reviewedBy' => 1,
            'active' =>1,
        ]);


        $center=Center::where('code','FY')->first();
        Notice::create([
            'date' => '2018-6-1',
            'centerId' =>$center->id,
            'title'=> '推廣學院舉辦「人生的11堂經驗分享」講座',
            'content'=>'<p>進修推廣學院將自106年8月22日起至9月28日止，連續舉辦11場免費講座，邀請來自各領域的優秀人士，分享自己的人生故事。11位講者都是曾在進修推廣學院學習的優秀學員，其在各自的生活或工作領域上有著精彩的歷練，憑藉自身努力的堅持與自我提升的向學之心，成就了更美好的自己。</p>',
            'reviewed' => 1,
            'reviewedBy' => 1,
            'active' =>1,
        ]);

        $center=Center::where('code','TP')->first();
        Notice::create([
            'date' => '2018-6-1',
            'centerId' =>$center->id,
            'title'=> '賀！進修推廣部「蔥油餅管理與實務研習班」第24期學員 何金水先生 榮獲FakeNews雜誌2016年最佳傑出企業獎',
            'content'=>'<p>賀！進修推廣部「蔥油餅管理與實務研習班」第24期學員 ──好蔥蔥油餅工業股份有限公司董事長 何金水先生 榮獲FakeNews雜誌2016年最佳傑出企業獎.<br>進修推廣為此深感榮耀、與有榮焉！</p>',
            'reviewed' => 1,
            'reviewedBy' => 1,
            'active' =>1,
        ]);
    }
    
    function readIndexRequest()
    {
        $request=request();
        

        $key='';
        if($request->key)  $key = $request->key;

        $keys=['west','east'];
        if(!$key) $key=$keys[0];
        else{
            if(!in_array($key,$keys)) abort(404);
        }
        
        
        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);
       
        $reviewed=true;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;
        

        $keyOptions = []; 
        if(!$this->isAjaxRequest()){
           
            $keyOptions =$this->centers->getKeyOptions();
        }  

       

        $params=[
          
            'key' => $key,
            'keyword' => $keyword,
            'active' => $active,
            'reviewed' => $reviewed,
            'page' => $page,
            'pageSize' => $pageSize

        ];

       

       
        return [
            

            'params' => $params,
           
            'keyOptions' => $keyOptions,
        ];
    }


    public function index()
    {
        $requestValues=$this->readIndexRequest();

        $params=$requestValues['params'];

        $key=$params['key'];
        $keyword=$params['keyword'];
        $active=$params['active'];
        $reviewed=$params['reviewed'];
        $page=$params['page'];
        $pageSize=$params['pageSize'];
       
        
        $notices=$this->notices->fetchNotices($key,$active,$reviewed);
       
        $notices=$this->notices->getOrdered($notices);
       
      
        $pageList = new PagedList($notices,$page,$pageSize);
        
      
        if($this->isAjaxRequest()){
           
            $model=[
                'model' => $pageList,
            ];
    
            return response()->json($model);
        }

        $keyOptions=$requestValues['keyOptions'];

        $model=[
            'title' => '公告管理',
            'menus' => $this->adminMenus('HomePageAdmin'),

            'list' => $pageList,            
            'keys' => $keyOptions,

            'params' => $params
        ];

        return view('notices.index')->with($model);
    }

    public function create()
    {
        $canReview=$this->canReview();
        
        $centerOptions = $this->centersCanAdmin()->map(function ($center) {
            return $center->toOption();
        })->all();

        $notice= Notice::init();
        $notice['centerId']=$centerOptions[0]['value'];

        $form=[
            'centerOptions'=> $centerOptions,
            'canReview' => $canReview,
            'notice' => $notice
        ];

        return response()->json($form);
      
    }

    public function store(NoticeRequest $request)
    {
        $defaultCenter=$this->defaultAdminCenter();
        if(!$defaultCenter)  $defaultCenter=Center::first();

       
        $values=$request['notice'];
       

        $values['updatedBy'] = $this->currentUserId();
        $values['key'] = $defaultCenter->key;

        if($this->canReview()){
            $values['reviewed'] = true;
        }
       
        $notice = Notice::create($values);
        return response()->json($notice);
    }

    public function edit($id)
    {
        $notice = Notice::findOrFail($id);

        $canReview=$this->canReview($notice);
        $notice->canDelete=$canReview;

        $centerOptions = $this->centersCanAdmin()->map(function ($center) {
            return $center->toOption();
        })->all();
       
        $form=[
            'centerOptions'=> $centerOptions,
            'canReview' => $canReview,
            'notice' => $notice,
        ];

        return response()->json($form);
       
        
    }

   

    public function update(NoticeRequest $request, $id)
    {
        $notice = Notice::findOrFail($id);
       
        if(!$this->canEdit($notice)) $this->unauthorized();

        $current_user=$this->currentUser();
       
        $values=$request['notice'];

        $values['updatedBy'] = $current_user->id;
        
        $notice->update($values);

        return response()->json();
    }

    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);
        $canReview=$this->canReview($notice);
        if(!$canReview) return $this->unauthorized();

        $current_user=$this->currentUser();
        $notice->update([
            'removed' => true,
            'updated_by' => $current_user->id
        ]);
       
       
        return response()->json();
    }

    
}
