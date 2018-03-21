<template>
    <div class="pdf-wrap" id="pdfWrap">
        <button @click="test">PDF</button>
        <div class="pdf-dom" id="pdfDom">

        </div>
        <div id="capture" style="padding: 10px; background: #f5da55">
            <h4 style="color: #000; ">Hello world!</h4>
        </div>
    </div>
</template>

<script>

import html2Canvas from 'html2Canvas';

export default {
    name:'DiscountIndexView',
    methods:{
        test(){
            html2Canvas(document.querySelector("#capture")).then(canvas => {
                Signup.print(canvas);
            });
        }
    }
    
}
</script>
