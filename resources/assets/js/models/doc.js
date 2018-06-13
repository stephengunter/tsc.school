
class Doc {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/docs';
    }
    static showUrl(id){
        return `${this.source()}/${id}`;
    }
    static createUrl(params) {
        let url = this.source() + '/create';
        return Helper.buildQuery(url, params);
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

 
    static index(){
        let url = this.source();
 
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

    

   
    static create(params) {
        let url = this.createUrl(params);
 
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

    static activeText(active) {
        if (Helper.isTrue(active)) return '公開';
        return '隱藏';
    }
    static activeLabel(active) {
        let style = 'label label-default';
        if (Helper.isTrue(active))  style = 'label label-info';
        let text = this.activeText(active);

        return `<span class="${style}" > ${text} </span>`;
    }

    static activeOptions() {
		return [{
			 text: '公開',
			 value: true
		}, {
			 text: '隱藏',
			 value: false
		}]
	}

    static reviewedOptions() {
		return [{
			 text: '已審核',
			 value: true
		}, {
			 text: '未審核',
			 value: false
		}]
	}
 
    
 }
 
 
 export default Doc;