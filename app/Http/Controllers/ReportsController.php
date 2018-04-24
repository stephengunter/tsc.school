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
use App\Services\CourseListReports;

use Carbon\Carbon;
use App\Core\Helper;

class ReportsController extends Controller
{
    public function __construct(Courses $courses,Terms $terms,Centers $centers,CourseListReports $courseListReports)
    {
        $this->terms=$terms;
        $this->centers=$centers;
        $this->courses=$courses;
        $this->courseListReports=$courseListReports;
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

    

    
}
