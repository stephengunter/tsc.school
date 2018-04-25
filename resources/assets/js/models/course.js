class Course {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/courses';
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
    static activeUrl() {
        return this.source() + '/active';
    }
    static shutdownUrl(id){
        return `${this.source()}/${id}/shutdown`;
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

    static upload(form){
        let url = this.source() + '/upload';
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

    static editInfo(id) {
        
        let url =  `${this.source()}/${id}/EditInfo`;
        
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

    static updateInfo(id,form){
        let url = `${this.source()}/${id}/UpdateInfo`;
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

    

    static active(form){
        let url = this.activeUrl();
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

    static shutdown(form,id){
        let url = this.shutdownUrl(id);
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

    static activeOptions() {
        
        return [{
            text: '正常開課',
            value: true
        }, {
            text: '課程停開',
            value: false
        }];
    }

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
        let style = 'label label-default';
        if (Helper.isTrue(active)) style = 'label label-info';
        let text = this.activeText(active);
        return `<span class="${style}" > ${text} </span>`;

    }
    
 
    
 }
 
 
 export default Course;