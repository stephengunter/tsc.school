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
                    繳費期限：
                       {{ signup.bill.deadLine }}
                    </h3>     
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

            <hr style="border:1px dashed #000; height:1px">


            <div  v-if="signup.bill.sevenCodes" v-show="!payed" class="row">
                <div class="col-sm-6">
                    
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
                                    {{ signup.bill.amount | formatMoney }} 
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
            title(){
                return Bill.titleText(this.payed);
            },
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
