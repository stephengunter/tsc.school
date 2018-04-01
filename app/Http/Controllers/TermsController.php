<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TermRequest;
use App\Term;
use App\Services\Terms;
use App\Services\Users;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class TermsController extends Controller
{
    
    public function __construct(Terms $terms )
    {
        $this->terms=$terms;
    }

    function canEdit()
    {
        return $this->currentUserIsDev();
    }

    function yearOption()
    {
        $thisYear=Helper::toTaipeiYear(Carbon::today()->year);
        $min=$thisYear - 5;
        $max=$thisYear + 1;
        $options=[];
        for ($i = $max; $i >= $min; $i--)
        {
            $item=[ 'text' => $i ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }
    function orderOption()
    {
        $options=[];
        for ($i = 1; $i <= 4; $i++)
        {
            $item=[ 'text' => $i ,  'value' => $i ];
            array_push($options,  $item);
        }

        return $options;
    }
    
    public function index()
    {
       
        $request=request();
        $active=true;
        if($request->active)  $active=Helper::isTrue($request->active);
      
     
        $terms=$this->terms->fetchTerms($active);
       
        $terms=$this->terms->getOrdered($terms);
       
      
        $pageList = new PagedList($terms);

        if($this->isAjaxRequest()){
            return response() ->json($pageList);
        }
       

        $menus=$this->adminMenus('MainSettings');
       
        return view('terms.index')->with([
            'title' => '學期設定管理',
            'menus' => $menus,
            'canEdit' => $this->canEdit(),
            'list' =>  $pageList
        ]);
    }

    public function create()
    {
        $thisYear=Helper::toTaipeiYear(Carbon::today()->year);

        $form=[
            'term' => Term::init($thisYear),
            'yearOptions' => $this->yearOption(),
            'orderOptions' => $this->orderOption(),
        ];

        return response() ->json($form);
      
    }

    public function store(TermRequest $request)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

       
        $values=$request->getValues();
       
        $errors=$this->validateTerm($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy'] = $current_user->id;
        $term = Term::create($values);
        return response() ->json($term);
    }

    public function edit($id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $term = Term::findOrFail($id);
       
        $form=[
            'term' => $term,
            'yearOptions' => $this->yearOption(),
            'orderOptions' => $this->orderOption(),
        ];

        return response() ->json($form);
       
        
    }

    function validateTerm($values)
    {
        $errors=[];

        $id=0;        
        if(array_key_exists ('id' ,$values)) $id=(int)$values['id'];        

        $number=$values['number'];
        $exist=$this->terms->getTermByNumber($number);
        if($exist && $exist->id!=$id){
            $errors['term.order'] = ['年度順序重複了'];
        }

        $closeDate=new Carbon($values['closeDate']);
        $openDate=new Carbon($values['openDate']);
        $birdDate=new Carbon($values['birdDate']);
        if($closeDate<=$openDate){
            $errors['term.openDate'] = ['日期順序錯誤'];
        }

        if($birdDate<=$openDate){
            $errors['term.openDate'] = ['日期順序錯誤'];
        }

        if($closeDate < $birdDate){
            $errors['term.closeDate'] = ['日期順序錯誤'];
        }

        return $errors;
    }

    public function update(TermRequest $request, $id)
    {
        $current_user=$this->currentUser();
        if(!$this->canEdit()) $this->unauthorized();

        $term = Term::findOrFail($id);
       
        $values=$request->getValues();

        $errors=$this->validateTerm($values);

        if($errors) return $this->requestError($errors);

        $values['updatedBy'] = $current_user->id;
        
        $term->update($values);
        return response() ->json();
    }

    
}
