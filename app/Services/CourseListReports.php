<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Course;
use App\Term;
use App\Center;
use App\Services\Courses;
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
    public function __construct(Courses $courses)                                          
    {
         $this->courses=$courses;

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
        $classTimes=$course->getClasstimes();
        foreach ($classTimes as $classtime) {
             $text .=  $classtime->fullText() .Chr(10);    

        }
        return $text;
    }
    private function teachers($course)
    {
        $text='';
        
        $teachers=$course->teachers;
        foreach ($teachers as $teacher) {
            $text .=  $teacher->getName() .Chr(10); 
            $experiences=$teacher->experiencesArray();
            for($i = 0; $i < count($experiences); ++$i) {
                if($experiences[$i]){
                    $text .= ' ● ' .$experiences[$i] .Chr(10);
                }
            }
          
            $text .= Chr(10) . $teacher->description;
        }
        return $text;
    }

    private function setCourse($sheet,$current_row,$course)
    {
        $data=array(
            $course->number,
            $course->name,
            $this->teachers($course),
            $course->description,
            $course->begin_date .  Chr(10) . '至'.  Chr(10) . $course->end_date,
            $this->classTimes($course),
            $course->hours,
            $course->tuition,
            // $course->open_date  .  Chr(10) . '至'.  Chr(10) . $course->close_date ,
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

    private function getCourseList($termId,$centerId)
    {
        $courseList=$this->courses->getAll();
        
        $courseList=$courseList->where('term_id',$termId)
                                ->where('center_id',$centerId)
                                ->where('reviewed',true); 

         return $courseList;
    }

    public function index()
    {
        if(!request()->ajax()){
            $menus=$this->menus($this->key);            
            return view('reports.courses')
                    ->with(['menus' => $menus]);
        }  

        $request = request();
        $termId=(int)$request->term; 
        $centerId=(int)$request->center;

        $courseList=$this->getCourseList($termId,$centerId)
                         ->with(['center','categories','teachers','classTimes'])
                         ->get();
        foreach ($courseList as $course) {
            $course->populateViewData();
            
        }

        return response() ->json(['courseList' => $courseList  ]);

        
    }

    public function store(Request $request)
    {
        $termId=$request['term'];
        $centerId=$request['center'];
        $courseList=$this->getCourseList($termId,$centerId)->get();
        

        if(!count($courseList)) dd('查無資料');

        $term=$courseList[0]->term;
        $center=$courseList[0]->center;
       
        $categoryCollection = collect([]);
        foreach ($courseList as $course) {
            $category=$course->defaultCategory();
            if($category){
                $exist=$categoryCollection->first(function ($item) use ($category) {
                return $item->id == $category->id;
                });
                if($exist){
                    $exist->courseList->push($course);
                }else{
                    $category->courseList=collect([$course]);
                    $categoryCollection->push($category);
                }
            }

        }

       
        $title=config('app.name').' '. $term->year . ' 學年度第 ' . $term->order .' 學期 '.$center->name;
        Excel::create($title.'課程清單', function($excel) use ($categoryCollection,$title){
            
            $excel->sheet('課程清單', function($sheet) use ($categoryCollection,$title){
               
                $colWidth=$this->getColWidth();               
                $sheet->setWidth($colWidth);
               
                $current_row=1;
                
                $data=array(
                   $title
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                $data=array(
                    '課程審核清冊'
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                foreach ($categoryCollection as $category) {
                    $this->setCategory($sheet,$current_row,$category);
                    $current_row +=1;

                    $this->setTableHead($sheet,$current_row);
                    $current_row +=1;

                    foreach ($category->courseList as $course) {
                        $this->setCourse($sheet,$current_row,$course);
                        $current_row +=1;
                    }


                }

            });

        })->download('xls');
    }
    
    
}