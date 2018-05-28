<template>
    <div id="signup">
        <div class="row" style="padding-top: 1em">
        </div>
        <div style="margin:50px 100px 15px 100px">
            <div class="row text-center" >
                <h3 v-if="payed">{{ title }}</h3>
                <h3 v-else>{{ title }}</h3>
            </div>
            <div class="row">
                <div class="col-sm-3" >
                    <h4>
                    學員姓名：
                    {{ signup.user.profile.fullname  }} 
                    </h4> 
                </div>
                <div v-if="!payed" class="col-sm-3">
                    <h4>
                    應繳金額：
                    {{ signup.amountShorted | formatMoney }} 
                    </h4> 
                </div>
                <div class="col-sm-3" >
                    <h4>
                    報名日期：
                     {{ signup.date }}
                    </h4> 
                </div>
                <div class="col-sm-3 text-right">  
                    <h4 v-if="!payed && bill">
                    繳費期限：
                       {{ bill.deadLine }}
                    </h4>     
                </div>
            </div>
            <div class="row">
                <div class="panel panel-default" :style="getStyle">
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
                                    <th v-if="false" > 教材費用</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in validDetails" :key="index">
                                    <td>{{ item.course.number }} </td>
                                    <td>{{ item.course.fullName }} </td>
                                    <td>{{  item.tuition | formatMoney }}   </td>
                                    <td v-if="false" >{{  item.cost | formatMoney}}   </td>    
                                    
                                </tr>
                            </tbody>
                        </table>

                        <hr>

                        <div class="row">
                            <div class="col-sm-4" >
                                <p v-if="hasDiscount">
                                    <label class="label-title">折扣：</label>
                                    {{ signup.discount }} 
                                    <span v-if="signup.points">
                                      &nbsp; {{ signup.pointsText }}
                                    </span>  
                                </p>        
                            </div>
                            <div class="col-sm-4">
                                <p  v-if="signup.amountPayed > 0">
                                    <label class="label-title">已繳金額：</label>
                                    {{ signup.amountPayed | formatMoney }} 
                                </p>
                                
                            </div>
                            <div class="col-sm-4">
                                <p v-if="bill" v-show="signup.amountShorted > 0">
                                    <label class="label-title">待繳金額：</label>
                                    {{ bill.amount | formatMoney }} 
                                </p>
                               
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div> <!--  End Row   -->

            <hr style="border:1px dashed #000; height:1px">


            <div  v-if="bill && !payed" class="row">
                <div v-if="sevenCodes.length" class="col-sm-6">
                    
                    <h4>超商專用條碼</h4>
                    <p>
                        <barcode  key="1" :value="sevenCodes[0]" :options="options"></barcode>
                    </p>

                    <p>
                        <barcode  key="2" :value="sevenCodes[1]" :options="options"></barcode>
                    </p>
                     <p>
                        <barcode  key="3" :value="sevenCodes[2]" :options="options"></barcode>
                    </p>
                   
                </div>
                <div class="col-sm-6">
                    <h4>{{ title }}</h4>
                    <table style="width:90%" class="table table-bordered">
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
                                    {{ signup.amount | formatMoney }} 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                   
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 text-right" >
                     {{ footerText  }}
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
                footerText:Bill.footerText(),
                options:{
                    height: 50,
                }
            }
        },
        computed:{
            payed(){
               if(!this.signup) return false;
               return Helper.isTrue(this.signup.payed);
            },
            bill(){
                if(!this.signup) return null;
                if(this.payed){
                    return null;
                }else{
                    return this.signup.bills.find((item) => {
                        return !Helper.isTrue(item.payed);
                    });    
                } 
                
            },
            validDetails(){
                if(!this.signup) return [];
                return this.signup.details.filter((item) => {
                    return !Helper.isTrue(item.canceled);
                });
            },
            title(){
                return Bill.titleText(this.payed);
            },
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            },
            sevenCodes(){
                if(!this.bill) return [];
                if(!this.bill.sevenCodes) return [];
                return this.bill.sevenCodes.split(',');
            },
            getStyle(){
                if(this.payed) return 'height:350px';
                return 'height:820px';
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
