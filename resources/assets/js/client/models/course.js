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

    static activeText(active) {
        if (Helper.isTrue(active)) return '正常開課';
        return '課程停開';
    }
    static activeLabel(active) {
        let style = 'tag is-black';
        if (Helper.isTrue(active)) style = 'tag is-info';
        let text = this.activeText(active);
        return `<span class="${style}" > ${text} </span>`;

    }
    
    
    
 
    
 }
 
 
 export default Course;