<template>
<div v-if="signup">
    <div style="font-size:16px">
        <div class="columns" >
            <div  class="column">
                <label class="label label-title">姓名</label>
                <p v-text="signup.user.profile.fullname"></p>                      
            </div>
            <div class="column">
                <label class="label label-title">報名日期</label>
                <p>
                    {{ signup.date }}
                </p>                   
            </div>
            <div class="column">
                <label class="label label-title">網路報名</label>
                <p>
                    <i v-if="isTrue(signup.net)" class="fa fa-check-circle" style="color:green"></i>
                </p>    
            </div>
            <div class="column">
                <label class="label label-title">狀態</label>
                <p v-html="$options.filters.signupStatusLabel(signup.status)" >
                    {{  signup.status | signupStatusLabel }}
                </p>   
               
            </div>
            <div class="column">
                <label class="label label-title">付款方式</label>
              
                <p  v-html="getPayRecord(signup)">

                </p>
            </div>
        </div>  <!-- End row--> 
        <div class="columns">
            <div class="column">
                <signup-details text="報名課程" :model="signup"> 
                    
                </signup-details>
                
            </div>
        </div>
        <div class="columns" >
            <div  class="column">
                <div v-if="hasDiscount">
                     <label class="label label-title">折扣：
                          {{ signup.discount }} 
                        <span>
                            &nbsp; {{ signup.pointsText }}
                        </span>  

                     </label>
                     
                </div>
                                
            </div>
            <div class="column">
                <label class="label label-title">金額：
                     {{ signup.amount | formatMoney }} 
                </label>
                              
            </div>
            
        </div>  <!-- End row-->      
         

    </div>
    
    
   
</div>    
</template>

<script>
    import Details from './detail-view';
    export default {
        name: 'ShowSignup', 
        components: {
          
            'signup-details':Details
        },
        props: {
            signup: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {

            }
        },
        computed:{
            hasDiscount(){
                return Signup.hasDiscount(this.signup);
            }
        }, 
        methods: { 
            isTrue(val){
                return Helper.isTrue(val);
            },
            getPayRecord(signup){
                let html='';
                if(signup.bill.payway) {
                    html += signup.bill.payway.name;
                    html += `<small>( ${signup.bill.payDate})</small>`;
                }
                return html;
            }
            
        }
    }
</script>
