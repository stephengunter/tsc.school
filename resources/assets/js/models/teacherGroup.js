class TeacherGroup {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/TeacherGroups';
    }
    static showUrl(id){
        return `${this.source()}/${id}`;
    }
    static teachersUrl(id){
        return `${this.source()}/${id}/teachers`;
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

    static teachers(id){
        return new Promise((resolve, reject) => {
            let url = this.teachersUrl(id)
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })

        })
    }

    static editTeacher(groupId){

        let url = `${this.source()}/${groupId}/EditTeacher`;
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {
                    resolve(response.data)
                })
                .catch(error => {
                    reject(error);
                })
        })
    }

    static addTeachers(groupId, teacherIds ){
        let form=new Form({
            teacherIds:teacherIds
        });

        let url = `${this.source()}/${groupId}/AddTeachers`;
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

    static removeTeacher(groupId, teacherId ){
        let form=new Form({
            teacherIds:[teacherId]
        });

        let url = `${this.source()}/${groupId}/RemoveTeacher`;
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
 
 
 export default TeacherGroup;