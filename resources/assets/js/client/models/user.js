class User {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/user';
    }
    static showUrl(){
        return `${this.source()}/profile`;
    }
    
   
    static editUrl() {
        return `${this.source()}/edit`;
    }
    static updateUrl() {
        return this.source() ;
    }
   

    static show() {
        return new Promise((resolve, reject) => {
            let url = this.showUrl()
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }
    
     
    
 
    static edit() {
        let url = this.editUrl();

        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                        resolve(response.data);
                })
                .catch(error => {
                        reject(error);
                })

        })
    }
 
    static update(form){
        let url = this.updateUrl();
        let method = 'put';
        return new Promise((resolve, reject) => {
            form.submit(method, url)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
        })
    }
 
 
    

    static roleLabels(roles){
     
        if(!roles) return '';

        if(Array.isArray(roles)){
            if(!roles.length) return '';
           
        }else{
            roles=roles.split(',');
        }
        
        let html='';
        roles.forEach((role)=>{
            html+= this.roleLabel(role);
        });

        return html;
    }

    static roleLabel(role){
        if(role=='Boss') return '<span class="label label-danger" > 主管 </span>';
        if(role=='Staff') return '<span class="label label-warning" > 職員 </span>';
        if(role=='Teacher') return '<span class="label label-info" > 教師 </span>';
        if(role=='Student') return '<span class="label label-success" > 學生 </span>';
        if(role=='Dev') return '<span class="label label-default" > 開發者 </span>';
        return '';
    }

    
    
 
    
 }
 
 
 export default User;