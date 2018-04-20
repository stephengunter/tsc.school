<template>
    <div id="signup">
        <div style="margin:50px;">
            <div class="columns">
                <div class="column" style="text-align: center;margin-top:35px">
                    <h2 class="title">
                        慈濟大學社會教育推廣中心課程繳費單
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
                <div class="panel" style="height:600px">
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
            <div class="columns">
                <div class="column">
                    <barcode :value="signup.bill.code" :options="barcodeOptions"></barcode>
                </div>
                <div class="column">
                    
                </div>
            </div>
            <div class="columns">
                <div class="column">
                    慈濟大學社會教育推廣中心 03-8565301轉1703、1704
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
