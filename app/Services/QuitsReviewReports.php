<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Course;
use App\Term;
use App\Center;
use App\Services\Courses;
use App\Services\Quits;
use App\Core\Helper;

use Excel;
use DB;


class QuitsReviewReports 
{
    protected $key='reports';
    private $cols=[
                    'A','B','C','D','E','F','G','H','I','J'
                ];
    private $width=[
       15,15,15,15,15,15,15,15,30,30
    ];
    public function __construct(Courses $courses,Quits $quits)                                          
    {
        $this->courses=$courses;
        $this->quits=$quits;
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
    private function setSummary($sheet,$current_row, $amount,$count)
    {
        $data=array(
                   '資料筆數：' . $count . ' 筆     '.  '總金額：' . $amount
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

    private function setFullBorder($sheet,$current_row, $col_count=0)
    {
      
        if(!$col_count){
           $col_count=count($this->cols);
            //$range=$this->getRange($this->cols[0],$current_row,$this->cols[$col_count-1],$current_row);
        }

       
        for($i = 0; $i < $col_count; ++$i) {
            $key=$this->getKey($this->cols[$i],$current_row);               
            $sheet->cell($key, function($cell) {
                 $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });
        }
    }
    private function setTableHead($sheet,$current_row)
    {
        $sheet->row($current_row, array(
                    '開課中心','學員姓名','原因','申請日期','退還學費',
                    '手續費','退款方式','應退金額','明細','匯款帳戶'
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

    private function buildReviews($sheet,$current_row)
    {
        $cols=[ '校長室','會計主任','會計組長','會計同仁','總務主任','出納組長','社教主任'];
        $sheet->row($current_row, $cols);

        $col_count=count($cols);
        
        $sheet->setHeight($current_row, 20);
        $range=$this->getRange($this->cols[0],$current_row,$this->cols[$col_count-1],$current_row);
      
        $sheet->cells($range, function($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
            $cells->setFontColor('#008000');
        });
        $this->setFullBorder($sheet,$current_row,$col_count);

        $current_row +=1;

        $sheet->setHeight($current_row, 50);
        $this->setFullBorder($sheet,$current_row,$col_count);

        return  $current_row;
    }

    private function quitDetailsInfo($quit)
    {
        $detailsText=[];
        foreach($quit->details as $quitDetail){
            array_push($detailsText,$quitDetail->getSummary());
        }
        return join(Chr(10), $detailsText);
    }

    private function setQuit($sheet,$current_row,$quit)
    {
        $accountInfo = $quit->getAccountInfo();
        $data=array(
            $quit->getCenter()->name,
            $quit->getStudentName(),
            $quit->getReasonText(),
            $quit->date,
            $quit->tuitions,
            $quit->fee,
            $quit->payway->name,
            $quit->amount(),
            $this->quitDetailsInfo($quit),
            str_replace('<br>',Chr(10),$accountInfo)
           
        );

        $detailsCount = count($quit->details);
        $row_height=$detailsCount * 25;
       
        if($accountInfo && $row_height==25) $sheet->setHeight($current_row, 50);
        
        $sheet->row($current_row, $data);

        $range=$this->rowRange($current_row);
        $sheet->getStyle($range)
                        ->getAlignment()
                        ->setWrapText(true);
        $sheet->cells($range, function($cells) {
           
            $cells->setValignment('center');
            $cells->setFontColor('#0000ff');
            $cells->setFontSize(10);
        });

       
        
        $this->setFullBorder($sheet,$current_row);
    }

    public function export($quits)
    {
        $period= $quits->min('date') . ' 至 ' .$quits->max('date');
        

        $title=config('app.company.fullname'). ' 退費審核報表 ' . $period;
       
        Excel::create($title, function($excel) use ($quits,$period,$title){
            
            $excel->sheet('退費審核報表', function($sheet) use ($quits,$title){
                
                $colWidth=$this->getColWidth();               
                $sheet->setWidth($colWidth);

                $count=count($quits);
               
                $current_row=1;
                
                $data=array(
                   $title
                );
                $this->setTitle($sheet, $current_row, $data);
                $current_row +=1;

                $amount=0;
                foreach($quits as $quit){
                    $amount+=$quit->amount();
                }
               

                $this->setSummary($sheet,$current_row,$amount,$count);
                $current_row +=1;


                $current_row = $this->buildReviews($sheet,$current_row);
                $current_row +=1;
                $current_row +=1;
                $this->setTableHead($sheet,$current_row);
                $current_row +=1;
                foreach ($quits as $quit) {
                   
                    $this->setQuit($sheet,$current_row,$quit);
                    $current_row +=1;

                }

                foreach ($quits as $quit) {
                   
                    $quit->status=0;
                    $quit->save();

                }

            });

        })->download('xls');
    }
    
    
}