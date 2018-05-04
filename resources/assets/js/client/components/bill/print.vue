<template>
    <div id="signup">
        <div class="row" style="padding-top: 1em">
        </div>
        <div style="margin:50px 100px 15px 100px">
            <div class="columns">
                <div class="column" style="text-align: center;margin-top:35px">
                    <h2 class="title" v-text="title">
                        
                    </h2>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <h5  class="title is-5">
                    應繳金額：
                    {{ signup.bill.amount | formatMoney }} 
                    </h5>
                </div>
                <div class="column">
                    <h5  class="title is-5">
                    報名日期：
                     {{ signup.date }}
                    </h5>
                </div>
                 <div class="column">
                    <h5  class="title is-5">
                    繳款期限：
                       {{ signup.bill.deadLine }}
                   </h5>    
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    <div class="panel" style="height:850px">
                        <div class="panel-heading panel-title heading" >
                            報名課程
                        </div>   
                        <div class="panel-block">
                            <table class="table">
                                <thead>
                                    <tr style="font-size:15px">
                                        <th>開課中心</th>
                                        <th>課程編號</th> 
                                        <th>課程名稱</th> 
                                        <th>課程費用</th>
                                        <th>教材費用</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in signup.details" :key="index">
                                        <td>{{ item.course.center.name }} </td>
                                        <td>{{ item.course.number }} </td>
                                        <td>{{ item.course.fullName }} </td>
                                        <td>{{  item.course.tuition | formatMoney }}   </td>
                                        <td>{{  item.course.cost | formatMoney}}   </td>    
                                        
                                    </tr>
                                </tbody>
                            </table>

                            <hr>

                            <div class="columns">
                
                                <div class="column">
                                
                                    <h5  v-if="hasDiscount"  class="title is-5">折扣：
                                        {{ signup.discount }} 
                                        <span>
                                            &nbsp; {{ signup.pointsText }}
                                        </span>  
                                    </h5>
                                </div>
                            
                                <div class="column">
                                    <h5 class="title is-5"> 應繳金額：{{ signup.bill.amount | formatMoney }}  元</h5>
                                
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div> <!--  End Row   -->

            <hr style="border:1px dashed #000; height:1px">


            <div class="columns">
                
                <div class="column">
                    <h4>超商專用條碼</h4>
                    <p>
                        <barcode  key="1" :value="sevenCodes[0]" :options="barcodeOptions"></barcode>
                    </p>

                    <p>
                        <barcode  key="2" :value="sevenCodes[1]" :options="barcodeOptions"></barcode>
                    </p>
                     <p>
                        <barcode  key="3" :value="sevenCodes[2]" :options="barcodeOptions"></barcode>
                    </p>
                </div>
                <div class="column">
                    <h5  class="title is-5">{{ title }}</h5>
                    <table style="width:90%" class="table is-bordered">
                        <thead>
                            <tr>
                                <th style="width:50%">繳費期限</th>
                                <th>應繳金額</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ signup.date }}
                                </td>
                                <td>
                                    {{ signup.bill.amount | formatMoney }} 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="columns">
                <div class="column">
                   
                </div>
                <div class="column" style="text-align: right;">
                    {{ footerText  }}
                </div>
            </div>
        </div>
    </div>    
</template>


<script>
    import html2Canvas from 'html2Canvas';
    import SignupDetailView from '../signup/detail-view.vue';
    export default{
        name:'BillPrint',
        components: {
           'signup-details':SignupDetailView
        },
        props: {
            signup: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                title:Bill.titleText(),
                footerText:Bill.footerText(),
                showPrint:false,
                barcodeOptions:{
                    height: 50,
                }
            }
        },
        computed:{
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            },
            sevenCodes(){
                if(!this.signup.bill.sevenCodes) return [];
                return this.signup.bill.sevenCodes.split(',');
            }
           
        }, 
        beforeMount(){
            
        },
        mounted(){
           
        },
        methods: {
            init(){
               
               
            },
            print() {
                let name='慈濟大學社會教育推廣中心課程繳費單' + this.signup.date;
                html2Canvas(document.querySelector("#signup")).then(canvas => {
                    Bill.print(canvas,name);
                });
               
            }
            
            
        },



    }
</script>
