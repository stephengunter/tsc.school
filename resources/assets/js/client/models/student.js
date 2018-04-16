class Student {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/students';
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

    static updateScores(students) {
        let form = new Form({
            students: students
        })
        return new Promise((resolve, reject) => {

            let url = this.source() + '/scores/update'
            form.post(url)
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    reject(error);
                })
        })

    }
     
    

    static getStatusText(status){
        status=parseInt(status);
       
        if(status==1) return '正常';
        if(status==0) return '已退出';
        
    }
    static getStatusStyle(status){
        status=parseInt(status);
        if(status==0) return 'default';
        if(status==1) return 'info';

        return ''
    }

    static statusLabel(status){
        let text=this.getStatusText(status)
        let style='label label-' + this.getStatusStyle(status)
        
        return `<span class="${style}" > ${text} </span>`
    }

    
 
    
 }
 
 
 export default Student;