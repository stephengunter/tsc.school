class Teacher {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/teacher';
    }
    static showUrl(id){
        return this.source();
    }
   
    static storeUrl() {
        return this.source();
    }
    static editUrl() {
        return `${this.source()}/edit`;
    }
    static updateUrl() {
        return this.source();
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

    
 
 
    

    

    static groupOptions() {
        return [{
            text: '教師群組',
            value: true
        }, {
            text: '一般教師',
            value: false
        }]
    }
    
    
    
 
    
 }
 
 
 export default Teacher;