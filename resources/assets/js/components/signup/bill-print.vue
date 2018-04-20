<template>
    
    <div id="signup">
        <div style="margin:50px;">
            <div class="row text-center" >
                <h3 v-if="payed">慈濟大學社會教育推廣中心課程繳費收據</h3>
                <h3 v-else>慈濟大學社會教育推廣中心課程繳費單</h3>
            </div>
            <div class="row">
                <div class="col-sm-4" >
                    <h3>
                    應繳金額：
                    {{ signup.bill.amount | formatMoney }} 
                    </h3> 
                </div>
                <div class="col-sm-4" >
                    <h3>
                    報名日期：
                     {{ signup.date }}
                    </h3> 
                </div>
                <div class="col-sm-4 text-right">  
                    <h3 v-if="!payed">
                    繳款期限：
                       {{ signup.bill.deadLine }}
                    </h3>     
                </div>
            </div>
            <div class="row">
                <div class="panel panel-default" style="height:450px">
                    <div class="panel-heading">
                        <h4>報名課程</h4>
                    </div>   
                    <div class="panel-body">
                        <table class="table">
                            <thead>
                                <tr style="font-size:15px">
                                    <th>課程編號</th> 
                                    <th>課程名稱</th> 
                                    <th>課程費用</th>
                                    <th>教材費用</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in signup.details" :key="index">
                                    <td>{{ item.course.number }} </td>
                                    <td>{{ item.course.fullName }} </td>
                                    <td>{{  item.course.tuition | formatMoney }}   </td>
                                    <td>{{  item.course.cost | formatMoney}}   </td>    
                                    
                                </tr>
                            </tbody>
                        </table>

                        <hr>

                        <div class="row">
                            <div class="col-sm-8" >
                                <p v-if="hasDiscount">
                                    <label class="label-title">折扣：</label>
                                    {{ signup.discount }} 
                                    <span v-if="signup.points">
                                      &nbsp; {{ signup.pointsText }}
                                    </span>  
                                </p>        
                            </div>
                            <div class="col-sm-4">
                                <label class="label-title">應繳金額：</label>
                                {{ signup.bill.amount | formatMoney }} 
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div> <!--  End Row   -->
            <div  v-if="signup.bill.sevenCodes" class="row">
                <div class="col-sm-12">
                    <barcode v-for="(item,index) in sevenCodes" :key="index" :value="item" :options="options"></barcode>
                   
                </div>
               
            </div>
            <div class="row">
                <div class="col-sm-12 text-right" >
                    慈濟大學社會教育推廣中心 03-8565301轉1703、1704
                </div>
                
            </div>
        </div>
    </div>
    
</template>
<script>
    import html2Canvas from 'html2Canvas';
    export default {
        name:'BillPrint',
        props: {
            signup: {
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
            payed(){
               if(!this.signup) return false;
               return Helper.isTrue(this.signup.bill.payed);
            },
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            },
            sevenCodes(){
                if(!this.signup.bill.sevenCodes) return [];
                return this.signup.bill.sevenCodes.split(',');
            }
        }, 
        beforeMount() {
           
        }, 
        methods: {
            print() {
               
                html2Canvas(document.querySelector("#signup")).then(canvas => {
                    Bill.print(canvas);
                });

               
            }
        }
    }
</script>
