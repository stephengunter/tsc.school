<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Doc;
use App\Services\Docs;
use App\Services\Files;
use App\Core\PagedList;
use Carbon\Carbon;
use App\Core\Helper;

class DocsController extends Controller
{
    
    public function __construct(Docs $docs,Files $files)
    {
        $this->docs=$docs;
        $this->files=$files;
    }
    
    public function index()
    {
       
        $docs=$this->docs->getAll();
      
        $pageList = new PagedList($docs);

        foreach($pageList->viewList as $doc){
            $doc->url='/docs/' . $doc->id;
        } 

        $model=[
            'title' => '下載專區',
            'topMenus' => $this->clientMenus(),
            'company' => $this->getCompany(),
          

            'list' => $pageList,
        ];

        

        return view('client.docs.index')->with($model);
    }

    public function show($id)
    {
        $doc = Doc::findOrFail($id);
      
        return $this->files->downloadPublicDoc($doc);
        
    }

   
}
