class Course {
    
 

    static classTimesHtml(course){
        if(course.classTimes && course.classTimes.length)
        {
            let html='';
            course.classTimes.forEach((item)=>{
                
                html += this.classTimeHtml(item) + '<br>';
                
            });
            return html;
            
        }
        return '';
    }

    static classTimeHtml(item){
        if(item.timeString){
            return `${item.weekday.title}&nbsp;${item.timeString}`;
        }
        return `${item.weekday.title}&nbsp;${item.on} ~ ${item.off}`;
    }
    
    
    
 
    
 }
 
 
 export default Course;