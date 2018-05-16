<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Core\PagedList;

use App\Term;
use App\Course;
use App\Center;

use App\Services\Terms;
use App\Services\Courses;
use App\Services\Centers;

use App\Services\Quits;
use App\Services\CourseListReports;
use App\Services\QuitsReviewReports;

use Carbon\Carbon;
use App\Core\Helper;

class ReportsController extends Controller
{
    public function __construct(Courses $courses,Terms $terms,Centers $centers, Quits $quits ,
                CourseListReports $courseListReports,QuitsReviewReports $quitsReviewReports)
    {
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->quits=$quits;
        $this->courseListReports=$courseListReports;
        $this->quitsReviewReports=$quitsReviewReports;
    }
    
    
    public function courses()
    {
        $request=request();

        $term=0;
        if($request->term)  $term=(int)$request->term;
        $selectedTerm = Term::findOrFail($term);

        $center=0;
        if($request->center)  $center=(int)$request->center;
        $selectedCenter = Center::findOrFail($center);


        $courses =  $this->courses->fetchCourses($term ,$selectedCenter)->get();
      
        

        return $this->courseListReports->export($selectedTerm,$selectedCenter,$courses);

    }

    public function quits()
    {
        $reviewingQuitsCount=$this->quits->getAll()->where('status' , 0 )->count();
        if($reviewingQuitsCount)    return view('errors')->with(['msg' => '匯出失敗.因為目前還有審核中的報表未處理.']);
      
         
        $quits = $this->quits->getAll()->where('status' , -1 )->get();
       
      
        return $this->quitsReviewReports->export($quits);

    }

    
}
