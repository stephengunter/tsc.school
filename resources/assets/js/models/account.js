class Account {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/accounts';
    }
    static showUrl(id){
        return `${this.source()}/${id}`;
    }
    static createUrl(user) {
        return this.source() + '/create?user=' + user;
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

 
    static create(user) {
        let url = this.createUrl(user);
 
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

    
 
    
 }
 
 
 export default Account;