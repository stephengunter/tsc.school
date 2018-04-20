<template>
<div>
   
    <div id="signup">
        <div>
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
                     
                </div>
            </div>
            <div class="columns">
                <div class="column">
                <div class="panel">
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
                            
                                <h5 v-if="hasDiscount"  class="title is-5">折扣：
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
            
        </div>
    </div>
    <div class="columns" style="padding-top:1.2em">
            
        <div class="column">
            
            <a href="#" @click.prevent="print" class="button is-outlined is-primary is-medium" >
                便利商店繳費
            </a>
                &nbsp;
            <a href="#" @click.prevent="credit" class="button is-outlined is-info is-medium" >
                信用卡繳費
            </a>
               

        </div>
        
    </div> 
</div>    
</template>


<script>
    import html2Canvas from 'html2Canvas';
    import SignupDetailView from '../signup/detail-view.vue';
    export default{
        name:'BillEdit',
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
                
                options:{
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
       
        methods: {
            init(){
               
               
            },
            print() {
                let url=`/bills/${this.signup.id}/print`;
                window.location=url;
               
            },
            credit(){
                // let url=`/bills/${this.signup.id}/credit`;
                // window.location=url;
            }
            
            
        },



    }
</script>
