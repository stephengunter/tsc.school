<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Course;
use App\Term;
use App\Center;
use App\Services\Courses;
use App\Services\Categories;
use App\Core\Helper;

use Excel;
use DB;


class CourseListReports 
{
    protected $key='reports';
    private $cols=[
                    'A','B','C','D','E','F','G','H'
                ];
    private $width=[
       12,28,33,33,12,23,12,12
    ];
    public function __construct(Courses $courses,Categories $categories)                                          
    {
        $this->courses=$courses;
        $this->categories=$categories;
	}

    private function getColWidth()
    {
        $cols=$this->cols;
        $width=$this->width;
      
        $colWidth=[];
        for($i = 0; $i < count($cols); ++$i) {
            $colWidth = array_add($colWidth, $cols[$i], $width[$i]);
        }
        return $colWidth;
    }

    private function getKey($col,$row)
    {
        return $col . $row;
    }
    private function getRange($col_from,$row_from,$col_end,$row_end)
    {
        $from=$this->getKey($col_from,$row_from);
        $end=$this->getKey($col_end,$row_end);
        return $from . ':' . $end;
    }
    private function rowRange($row)
    {
        $cols=$this->cols;
        $last=count($cols)-1;

        return $this->getRange($cols[0],$row,$cols[$last],$row);
    }

    private function setTitle($sheet,$current_row,$data)
    {
        $sheet->row($current_row, $data);
        $sheet->setHeight($current_row, 20);
        $range=$this->rowRange($current_row);
        $sheet->mergeCells($range);
        $key=$this->getKey('A',$current_row);               
        $sheet->cell($key, function($cell) {
            $cell->setAlignment('center');
            $cell->setValignment('center');
            $cell->setFontColor('#0000ff');
        });
    }
    private function setCategory($sheet,$current_row,$category)
    {
        $data=array(
                   '◎' . $category->name
                );
        $sheet->row($current_row, $data);
        $sheet->setHeight($current_row, 20);
        $range=$this->rowRange($current_row);
        $sheet->mergeCells($range);
        $key=$this->getKey('A',$current_row);               
        $sheet->cell($key, function($cell) {
            $cell->setValignment('center');
            $cell->setFontColor('#0000ff');
        });
    }

    private function setFullBorder($sheet,$current_row)
    {
        $cols=$this->cols;
        for($i = 0; $i < count($cols); ++$i) {
            $key=$this->getKey($cols[$i],$current_row);               
            $sheet->cell($key, function($cell) {
                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
        }
    }
    private function setTableHead($sheet,$current_row)
    {
        $sheet->row($current_row, array(
                    '編號','課程名稱','授課教師','課程簡介','課程日期','上課時間',
                    '總時數','課程費用'
                ));
        $sheet->setHeight($current_row, 20);
        $range=$this->rowRange($current_row);
        $sheet->cells($range, function($cells) {
             $cells->setAlignment('center');
             $cells->setValignment('center');
             $cells->setFontColor('#008000');
        });
        $this->setFullBorder($sheet,$current_row);
    }

    private function classTimes($course)
    {
        $text='';
      
        foreach ($course->classTimes as $classtime) {
             $text .=  $classtime->fullText() .Chr(10);    

        }
        return $text;
    }
    private function teachers($course)
    {
        $text='';
        
        $allTeachers= $course->allTeachers();
        
        foreach ($allTeachers as $teacher) {
            $text .=  $teacher->getName(); 

            $summary= $teacher->getSummary();
            if($summary)  $text .= '  (' . $teacher->getSummary() . ')';

            $text .= Chr(10); 
        }
        return $text;
    }

    private function setCourse($sheet,$current_row,$course)
    {
        $data=array(
            $course->number,
            $course->fullName(),
            $this->teachers($course),
            $course->description,
            $course->beginDate .  Chr(10) . '至'.  Chr(10) . $course->endDate,
            $this->classTimes($course),
            $course->hours,
            $course->tuition
           
        );

        $sheet->setHeight($current_row, 360);
        $sheet->row($current_row, $data);

        $range=$this->rowRange($current_row);
        $sheet->getStyle($range)
                        ->getAlignment()
                        ->setWrapText(true);
        $sheet->cells($range, function($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('top');
            $cells->setFontColor('#0000ff');
            $cells->setFontSize(10);
        });

        $key=$this->getKey('C',$current_row);
        $sheet->cell($key, function($cell) {
              $cell->setAlignment('left');
        });
        
        $this->setFullBorder($sheet,$current_row);
    }

  

    function getCategories($courses)
    {
        $categoryIds = array_unique($courses->pluck('categoryId')->toArray());
      
        return $this->categories->getByIds($categoryIds)->get();
    }

    public function export(Term $term , Center $center,$courses)
    {
        
        $categories=$this->getCategories($courses);
       
        $title=config('app.company.fullname').' '. $term->year . ' 學年度第 ' . $term->order .' 學期 '.$center->name;
        Excel::create($title.'課程清單', function($excel) use ($courses,$categories,$title){
            
            $excel->sheet('課程清單', function($sheet) use ($courses,$categories,$title){
                
                $colWidth=$this->getColWidth();               
                $sheet->setWidth($colWidth);
               
                $current_row=1;
                
                $data=array(
                   $title
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                $data=array(
                    '課程清冊'
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                foreach ($categories as $category) {
                    $this->setCategory($sheet,$current_row,$category);
                    $current_row +=1;

                    $this->setTableHead($sheet,$current_row);
                    $current_row +=1;
                    
                   
                    $categoryCourses=$courses->where('categoryId',$category->id);
                   
                    foreach ($categoryCourses as $course) {
                        $this->setCourse($sheet,$current_row,$course);
                        $current_row +=1;
                    }


                }

            });

        })->download('xls');
    }
    
    
}