import jsPDF from 'jsPdf';

class Signup {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    static source() {
        return '/manage/signups';
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

    static courses(params){
        let url = this.source() + '/courses';
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

    static report(params){
        let url = this.source() + '/report';
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

    static getByUser(user){
       
        let url = this.source() + `/GetByUser/${user}`;

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

    static initPrint(id){
        let url =`${this.source()}/${id}/print-bill`;

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
    
    static print(canvas){
        
        let contentWidth = canvas.width;
        let contentHeight = canvas.height;
        let pageHeight = contentWidth / 578.28 * 757.701;
        let leftHeight = contentHeight;
        let position = 0;
        let imgWidth = 578.28;
        let imgHeight = 578.28 / contentWidth * contentHeight;

        let pageData = canvas.toDataURL('image/jpeg', 1.0);

        let PDF = new jsPDF('1', 'pt', 'a4');
       
        if (leftHeight < pageHeight) {
           PDF.addImage(pageData, 'JPEG', 0, 0, imgWidth, imgHeight);
        }else {
            while (leftHeight > 0) {
                PDF.addImage(pageData, 'JPEG', 0, position, imgWidth, imgHeight);
                leftHeight -= pageHeight;
                position -= 841.89;
                if (leftHeight > 0) {
                    PDF.addPage();
                }
            }
        }
        PDF.autoPrint();
        window.open(PDF.output('bloburl'), '_blank');
    }
    
    
    static getStatusText(status){
        status=parseInt(status);
        if(status==0) return '待繳費';
        if(status==1) return '已繳費';
        if(status==-1) return '已取消';
            return '';
    }
    static getStatusStyle(status){
        status=parseInt(status);
        if(status==0) return 'default';
        if(status==1) return 'info';
        if(status==-1) return 'warning';

        return ''
    }
    
    static statusLabel(status){
        let text=this.getStatusText(status)
        let style='label label-' + this.getStatusStyle(status)
        
        return `<span class="${style}" > ${text} </span>`
    }

    static courseNames(signup){
        let names= signup.details.map((item)=>{
            return item.course.number + ' ' +  item.course.fullName;
        });
      
        return  names.join().replace(',','<br>');
    }

    static hasDiscount(signup)
    {
        let points= Helper.tryParseInt(signup.points);
        if(!points) return false;
        if(points==100) return false;
        return true;
    }

    static hasPayed(signup){
        return parseInt(signup.status)!=0;
    }

    static hasQuit(signup){
        return parseInt(signup.status) < 0;
    }

    static payRecords(signup){
        return signup.bills.filter((item) => {
            return Helper.isTrue(item.payed);
        });
    }

    static hasPayRecords(signup){
        let payRecords=this.payRecords(Signup);
        return payRecords.length > 0;
    }
 
    
 }
 
 
 export default Signup;