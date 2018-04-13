import jsPDF from 'jsPdf';

class Bill {
    constructor(data) {
 
        for (let property in data) {
           this[property] = data[property];
        }
 
    }
 
    
    
    static print(canvas,name){
        
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

        PDF.save(name);
        
    }
    
    
    
    
 }
 
 
 export default Bill;