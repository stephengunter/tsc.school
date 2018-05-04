<template>
    <div>
        <div>
            <button @click="print">print</button>
        </div>
    <div id="certificate">
        <div class="row" style="padding-top: 1em">
        </div>
        <div style="margin:50px 100px 15px 100px">
            <div class="row text-center" >
                <h1>{{ title }}</h1>
            </div>
            <div class="row" >
                <div class="text-right">
                    95年5月29日第21次校務會議通過 <br> 教育部91年3月14台(九一)高(二)字第九一０三二三一三號函准予核備 
                </div>
            </div>
            <div class="row" style="padding-top: 1em">
                <p style="line-height: 45px;font-size:22px">
                    何金水 君 自民國107年9月7日 至 民國107年12月23日，於本校社會教育推廣中心進修『生活輔導人員進修班』，修業期滿成績及格，特此證明。
                </p>
            </div>
       </div>    
    </div>
    </div>
</template>
<script>
    import html2Canvas from 'html2Canvas';
    export default {
        name:'StudentCertificate',
        props: {
            student: {
              type: Object,
              default: null
            }
        },
        data() {
            return {
                
                options:{
                    height: 50,
                }
            }
        },
        computed:{
            title(){
                return '慈濟大學社會教育推廣中心 課程結業證書';
            },
        }, 
        beforeMount() {
           
        }, 
        methods: {
            print() {
               
                html2Canvas(document.querySelector("#certificate")).then(canvas => {
                    Bill.print(canvas);
                });

               
            }
        }
    }
</script>
