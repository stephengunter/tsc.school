class Quit {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/quits';
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
        return this.source() + `/${id}`;
    }
    static reviewUrl(id) {
        return this.source() + '/review';
    }
    static finishUrl(id) {
        return this.source() + '/finish';
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
     
    static create(signupId) {
        let url = this.createUrl();
        let params={
            signup:signupId
        };
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




    static updateStatuses(form){
        let url = this.source() + '/updateStatuses';
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

    static updatePS(form){
        let url = this.source() + '/updatePS';
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

    static percentsOptions(){
        return [
            { text:'-------', value:'' },
            { text:'9成', value:'90' },
            { text:'7成', value:'70' },
            { text:'5成', value:'50' },
        ];
    }

    static statusOptions() {
        
        return [{
                text: '待處理',
                value: -1
            }, {
                text: '審核中',
                value: 0
            }, {
                text: '已審核',
                value: 1
            }, {
                text: '已完成',
                value: 2
            }
        ];
    }

    static getStatusText(status){
        status=parseInt(status);
        if(status==-1) return '待處理';
        if(status==0) return '審核中';
        if(status==1) return '已審核';
        if(status==2) return '已完成';
      
            return '';
    }
    static getStatusStyle(status){
        status=parseInt(status);
        if(status==-1) return 'default';
        if(status==0) return 'warning';
        if(status==1) return 'info';
        if(status==2) return 'success';
      

        return ''
    }
    
    static statusLabel(status){
        let text=this.getStatusText(status)
        let style='label label-' + this.getStatusStyle(status)
        
        return `<span class="${style}" >${text}</span>`
    }

    static detailSummary(detail,withNumber=true){
        let courseName=detail.course.fullName;
        if(withNumber) courseName = detail.course.number + ' ' + courseName;
        let tuition=Helper.formatMoney(detail.tuition);
        return `${courseName} ${tuition} (${detail.percentsText})`
    }
 
    
 }
 
 
 export default Quit;