class Menus {

	static getIcon(title) {
        title = title.toLowerCase();
        let html = '';
        switch (title) {
            case 'users':
                html = '<i class="fa fa-user-circle"></i>';
                break;
            case 'usersadmin':
                html = '<i class="fa fa-user-circle"></i>';
                break;
            case 'contactinfo':
                html = '<i class="fa fa-envelope"></i>';
                break;
            case 'teachers':
                html = '<i class="fa fa-user-circle"></i>';
                break;
            case 'centers':
                html = '<i class="fa fa-university" aria-hidden="true"></i>';
                break;
            case 'categories':
                html = '<i class="fa fa-th-list"></i>';
                break;
            case 'terms':
                html='<i class="fa fa-calendar"></i>';
                break;
            
            case 'courses':
                html = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
                break;    
            case 'coursesadmin':
                html = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>';
                break;
            case 'classtimes':
                html = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
                break;
            case 'credit_courses':
                html = '<i class="fa fa-graduation-cap" aria-hidden="true"></i>';
                break;
            case 'lessons':
                html = '<i class="fa fa-calendar-check-o" aria-hidden="true"></i>';
                break;
            case 'signups':
                html = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                break;
            case 'signupsadmin':
                html = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                break;
            case 'bills':
                html = '<i class="fa fa-credit-card"></i>';
            break;
            case 'tuitions':
                html = '<i class="fa fa-money" aria-hidden="true"></i>';
                break;
            case 'quits':
                html = '<i class="fa fa-backward" aria-hidden="true"></i>';
                break;
            case 'mainsettings':
                html = '<i class="fa fa-cogs" aria-hidden="true"></i>';
                break;
            case 'settings':
                html = '<i class="fa fa-cog" aria-hidden="true"></i>';
                break;
            case 'discounts':
                html = 'euro';
                break;
            case 'admins':
                html = '<i class="fa fa-key" aria-hidden="true"></i>';
                break;
           case 'volunteers':
                html = '<i class="fa fa-handshake-o" aria-hidden="true"></i>'
                break;    
            case 'admissions':
                html = '<i class="fa fa-list-alt" aria-hidden="true"></i>';
                break;
            case 'registers':
                html = '<i class="fa fa-registered" aria-hidden="true"></i>';
                break;
            case 'students':
                html = '<i class="fa fa-user-circle" aria-hidden="true"></i>';
                break;
            case 'leaves':
                html = '<i class="fa fa-calendar-o" aria-hidden="true"></i>';
                break;
            case 'statuses':
                html = '<i class="fa fa-check-circle" aria-hidden="true"></i>';
                break;
            case 'notices':
                html = '<i class="fa fa-comments-o" aria-hidden="true"></i>';
                break;
            case 'reports':
                html = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
                break;
            case 'scores':
                html = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                break;
            case 'process':
                html='<i class="fa fa-list-ol"></i>';
                break;

        }

        return html
    }
    static getTitleHtml(title) {
       
        return this.getIcon(title) + ' ' + this.getTitleText(title);
    }

    static getTitleText(title){
        title = title.toLowerCase();
        let text= ''; 
        switch (title) {
            case 'users':
                text= '使用者管理';
                break;
            case 'usersadmin':
                text= '使用者管理';
                break;
            case 'teachers':
                text= '教師管理';
                break;
            case 'terms':
               text='學期設定管理';
               break;
            case 'coursesadmin':
                text=  '課程管理';
                break;
            case 'courses':
                text=  '課程管理';
                break;
            case 'credit_courses':
                text=  '學分班課程管理';
                break;     
            case 'students':
                text = '學員管理';
                break;
            case 'signupsadmin':
                text=  '報名管理';
                break;    
            case 'signups':
                text= '報名管理';
                break;
            case 'refunds':
                text= '退費管理';
                break;
            case 'discounts':
                text= '折扣管理';
                break;
            case 'mainsettings':
                text= '主要設定';
                break;
            case 'settings':
                text= '基本設定';
                break;
            case 'admins':
                text= '權限管理';
                break;
            case 'notices':
                text= '公告管理';
                break;
            break
            case 'reports':
                text= '報表';
                break;
            break
        }

        return text;
            
       
    }
	

	
   

   
}


export default Menus;