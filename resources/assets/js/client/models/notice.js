
class Notice {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/notices';
    }
    static showUrl(id){
        return `${this.source()}/${id}`;
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

 
    

    
     
 
    
 }
 
 
 export default Notice;