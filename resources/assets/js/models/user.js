class User {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/users';
    }
    static showUrl(id){
        return `${this.source()}/${id}`;
    }
    static createUrl() {
        return this.source() + '/create';
    }
    static storeUrl() {
        return this.source();
    }
    static editUrl(id) {
        return `${this.source()}/${id}/edit`;
    }
    static updateUrl(id) {
        return this.source() + `/${id}`;
    }
    static deleteUrl(id){
        return this.source() + '/delete/' + id;
    }
    static reviewUrl(id) {
        return this.source() + '/review';
    }

    static show(id) {
        return new Promise((resolve, reject) => {
            let url = this.showUrl(id)
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }

    static import(form){
        let url = this.source() + '/import';
        return new Promise((resolve, reject) => {
            axios.post(url, form)
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                   
                    reject(error);
                })

        })
    }
 
    static index(params){
        let url = this.source();
        url=Helper.buildQuery(url, params);
 
 
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
     
    static create() {
        let url = this.createUrl();
 
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

    static find(email,phone,sid){
        let form=new Form({
            email:email,
            phone:phone,
            profile:{
                sid:sid
            }
            
        });
        
        let url = this.source() + '/find';
        let method = 'post';
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
     
    static store(form){
        let url = this.storeUrl();
        let method = 'post';
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
 
    static edit(id) {
        let url = this.editUrl(id);

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
 
    static update(id,form){
        let url = this.updateUrl(id);
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
 
 
    static remove(id) {
        let url = this.deleteUrl(id);
        
        return new Promise((resolve, reject) => {
            axios.delete(url)
                .then(response => {
                        resolve(response.data);
                })
                .catch(error => {
                        reject(error);
                })

        })
    }

    static review(form){
        let url = this.reviewUrl();
        let method = 'post';
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

    

    static groupOptions() {
        return [{
            text: '教師群組',
            value: true
        }, {
            text: '一般教師',
            value: false
        }]
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