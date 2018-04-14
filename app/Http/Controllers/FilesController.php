<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Doc;
use App\Services\Files;

use App\Core\PagedList;
use App\Core\Helper;
use DB;
use File;

use Storage;
use Illuminate\Support\Facades\Input;

class FilesController extends Controller
{
    public function __construct(Files $files)        
    {
        $this->files=$files;
    }

    public function download($name)
    {
        return $this->files->downloadTemplate($name);
    }

    function saveFile($file, $fileName)
    {
        $disk=Storage::disk('local');
        $save_path= '/templates/' . $fileName;
        $disk->put($save_path ,  File::get($file));
        return $save_path;
    }

    public function store(Request $form)
    {
        $errors=[];
      
        if(!$form->hasFile('file')){
            $errors['msg'] = ['無法取得上傳檔案'];
        } 

        if($errors) return $this->requestError($errors);

        $file=Input::file('file');   
      
        dd($file->getClientOriginalExtension());


       
        $this->saveFile($file,'fghjhgf');
       

        return response()->json();

       
    }

    //public async Task<IActionResult> Download(string name)
    //{
        //下載範本

        // var docFile =  filesService.FindDocFileByName(name);
        // if (docFile == null) return NotFound();

        // string folderPath = UploadFilesPath;
        // var filePath = Path.Combine(folderPath, docFile.Path);

        // string extension = docFile.Type;
        // string contentType = GetContentType(extension);


        // string fileDownloadName = String.Format("{0}{1}", docFile.Title, extension);

        // var memory = new MemoryStream();
        // using (var stream = new FileStream(filePath, FileMode.Open))
        // {
        //     await stream.CopyToAsync(memory);
        // }
        // memory.Position = 0;
        // return File(memory, contentType, fileDownloadName);
    //}


   


    

   

}
