<?php

namespace App\Services;

use App\Category;
use Excel;

class Categories 
{

    public function getAll()
    {
        return Category::where('removed',false); 
    }

    public function getById($id)
    {   
        return $this->getAll()->where('id',$id)->first();
    }  

    public function getByIds($ids)
    {   
        return $this->getAll()->whereIn('id',$ids);
    }   


    public function createCategory(Category $category)
    {
        if(!$category->importance)
        {
            $min=$this->getMinImportance();

            $category->importance=$min - 1;
        }

        $category->save();
        return $category;
    }
    public function removeCategory(Category $category, $updatedBy)
    {
        $category->updatedBy=$updatedBy;
        $category->removed=true;
        $category->save();
    }
    public function fetchCategories(bool $active=true)
    {
        return $this->getAll()->where('active',$active);
    }

    

    public function getCategoryByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
         
    }

    public function updateImportance($id,$importance)
    {
        $category=Category::find($id);
        $category->importance=$importance;
        $category->save();
    }

    public function getOrdered($terms)
    {
        return $terms->orderBy('top','desc')->orderBy('importance','desc');
    }

    function getMinImportance()
    {
        $min = $this->getAll()->min('importance');
        if(!$min) return 0;
        return $min;
    }

    public function options($withEmpty=true)
    {
        $categories= $this->fetchCategories()->get();
        $options = $categories->map(function ($category) {
            return $category->toOption();
        })->all();

        if($withEmpty) array_unshift($options, ['text' => '所有分類' , 'value' =>'0']);
        
        return $options;
    }

    public function forEditCourseOptions()
    {
        $categories= $this->fetchCategories()->where('top',false)->get();
        $options = $categories->map(function ($category) {
            return $category->toOption();
        })->all();

        
        return $options;
    }
    
    public function importCategories($file,$updatedBy)
    {
        $err_msg='';

        $excel=Excel::load($file, function($reader) {             
            $reader->limitColumns(16);
            $reader->limitRows(100);
        })->get();

        $categoryList=$excel->toArray()[0];
       
        for($i = 1; $i < count($categoryList); ++$i) {
            $row=$categoryList[$i];
            
            $name=trim($row['name']);
            if(!$name){
               continue;
            }

            $exist_category=null;

            $code=trim($row['code']);
            if(!$code){
                $err_msg .= '必須填寫分類代碼';
                continue;
            }

            $exist=Category::where('code',$code)->first();
            if($exist){
                $err_msg .= '分類代碼' . $code .'重複了';
                continue;
            }
            
                        
            $importance=(int)trim($row['importance']);
            $top=(int)trim($row['top']);
         
            $values=[
                'name' => $name,
                'code' => $code,
                'importance' => $importance,
              
                'top' => $top,
                'active' => true,
                'updatedBy' => $updatedBy
            ];

            if($exist_category){                                    
               
                $exist_category->update($values);
            
            }else{                
                $category=Category::create($values);
            
            }
        }  //end for  

        return $err_msg;
    }
    
}