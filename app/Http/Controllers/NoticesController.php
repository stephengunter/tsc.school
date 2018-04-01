<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\NoticeRequest;
use App\Notice;
use App\Services\Notices;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class NoticesController extends Controller
{
    public function __construct(Notices $notices)
    {
        $this->notices=$notices;
    }

    function canEdit()
    {
        return $this->currentUserIsDev();
    }

    function seed()
    {
        Notice::create([
            'title' => '行政院會通過「法院組織法」部分條文修正草案',
            'content' => '行政院會今（15）日通過司法院函送的「法院組織法」第83條修正草案及「法院組織法」部分條文修正草案，將由行政院與司法院會銜送請立法院審議。',

        ]);


        Notice::create([
            'centerId' => 1,
            'title' => '花蓮中心4/16舉辦學員迎新會',
            'content' => '行政院長賴清德今（18）日視察林口發電廠，盼社會各界更加了解使用「超超臨界」機組及「乾淨煤」發電的林口電廠整體情況。賴院長強調，林口電廠的排放標準之所以能夠接近天然氣發電，是因採用「淨煤技術」及「超超臨界」發電機組，並搭配最先進的空污防制設備。空污攸關國人健康，政府非常重視，會使用最先進設備及技術，希望減輕民眾疑慮。',
            
        ]);
    }
    
    public function index()
    {
        //$this->seed();
        dd('notices');
        // $request=request();
        // $active=true;
        // if($request->active)  $active=Helper::isTrue($request->active);
      
     
        // $notices=$this->notices->fetchNotices($active);
       
        // $notices=$this->notices->getOrdered($notices);
       
      
        // $pageList = new PagedList($notices);

        // if($this->isAjaxRequest()){
        //     return response() ->json($pageList);
        // }
       

        // $menus=$this->adminMenus('MainSettings');
       
        // return view('notices.index')->with([
        //     'title' => '學期設定管理',
        //     'menus' => $menus,
        //     'canEdit' => $this->canEdit(),
        //     'list' =>  $pageList
        // ]);
    }

    public function create()
    {
        $thisYear=Helper::toTaipeiYear(Carbon::today()->year);

        $form=[
            'notice' => Notice::init($thisYear),
            'yearOptions' => $this->yearOption(),
            'orderOptions' => $this->orderOption(),
        ];

        return response() ->json($form);
      
    }

    public function store(NoticeRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

       
        $values=$request->getValues();
       
        $errors=$this->validateNotice($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy'] = $current_user->id;
        $notice = Notice::create($values);
        return response() ->json($notice);
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $notice = Notice::findOrFail($id);
       
        $form=[
            'notice' => $notice,
            'yearOptions' => $this->yearOption(),
            'orderOptions' => $this->orderOption(),
        ];

        return response() ->json($form);
       
        
    }

    function validateNotice($values)
    {
        $errors=[];

        $id=0;        
        if(array_key_exists ('id' ,$values)) $id=(int)$values['id'];        

        $number=$values['number'];
        $exist=$this->notices->getNoticeByNumber($number);
        if($exist && $exist->id!=$id){
            $errors['notice.order'] = ['年度順序重複了'];
        }

        $closeDate=new Carbon($values['closeDate']);
        $openDate=new Carbon($values['openDate']);
        $birdDate=new Carbon($values['birdDate']);
        if($closeDate<=$openDate){
            $errors['notice.openDate'] = ['日期順序錯誤'];
        }

        if($birdDate<=$openDate){
            $errors['notice.openDate'] = ['日期順序錯誤'];
        }

        if($closeDate < $birdDate){
            $errors['notice.closeDate'] = ['日期順序錯誤'];
        }

        return $errors;
    }

    public function update(NoticeRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $notice = Notice::findOrFail($id);
       
        $values=$request->getValues();

        $errors=$this->validateNotice($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy'] = $current_user->id;
        
        $notice->update($values);
        return response() ->json();
    }

    
}
