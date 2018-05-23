<template>
<div v-if="quit">
    <div class="show-data">
        <div class="row" >
            <div class="col-sm-3">
                <label class="label-title">姓名</label>
                <p v-html="fullname">
                    
                </p>                      
            </div>
            <div  class="col-sm-3">
                <label class="label-title">應退金額</label>
                <p>
                    {{ quit.amount | formatMoney  }}
                </p>                      
            </div>
            <div  class="col-sm-3">
                <label class="label-title">原因</label>
                <p>
                    {{ quit.reason }} 
                </p>                      
            </div>
            <div  class="col-sm-3">
                <label class="label-title">狀態</label>
                <p v-html="$options.filters.quitStatusLabel(quit.status)"></p>   
                          
            </div>
        </div>  <!-- End row--> 
        
    </div>
    <div class="show-data">
        <div class="row" >
            <div class="col-sm-12">
                <label class="label-title">明細</label>
                <p v-html="getDetails(quit)">
                    
                </p>                      
            </div>
        </div>    
    </div>    
    <div class="show-data">
        <div class="row" >
            <div  class="col-sm-3">
                <label class="label-title">申請日期</label>
                <p v-text="quit.date"></p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">退還學費</label>
                <p >
                     {{ quit.tuitions | formatMoney  }}
                </p>                   
            </div>
            <div class="col-sm-3">
                <label class="label-title">手續費</label>
                <p >
                     {{ quit.fee | formatMoney  }}
                </p>                   
            </div>
            <div class="col-sm-3">
                <label class="label-title">退款方式</label>
                <p>
                   {{ quit.payway.name }}
                </p>    
            </div>
        </div>  <!-- End row--> 
        <div  class="row">
            <div class="col-sm-3">
                <label class="label-title">銀行名稱</label>
                <p >
                   {{ quit.account_bank }}
                </p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">分行</label>
                <p >
                   {{ quit.account_branch }}
                </p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">戶名</label>
                <p >
                   {{ quit.account_owner }}
                </p>                      
            </div>
            <div class="col-sm-3">
                <label class="label-title">銀行帳號</label>
                <p >
                   {{ quit.account_number }}
                </p>                      
            </div>
        </div>   <!-- End row-->
        
        <div  class="row">
            <div class="col-sm-3">
                <label class="label-title">金資代碼</label>
                <p >
                   {{ quit.account_code }}
                </p>                      
            </div>
            <div class="col-sm-9">
                <label class="label-title">備註</label>
                <p>
                    {{ quit.ps }}
                    &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editPS">
                        <i class="fa fa-edit"></i>
                    </button>
                </p>                      
            </div>
            
        </div>   <!-- End row-->
        
        
    </div>


   
</div>    
</template>

<script>
    import QuitDetails from './details.vue';
    export default {
        name: 'ShowQuit', 
        props: {
            quit: {
               type: Object,
               default: null
            },
            user: {
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
            version: {
              type: Number,
              default: 0
            },
        },
        components: {
            'quit-details':QuitDetails
        },
        data() {
            return {

            }
        },
        computed:{
            hasScore(){
                if(!this.quit) return false;
                return Helper.tryParseInt(this.quit.score) > 0;
            },
            fullname(){
                if(this.user) return this.user.profile.fullname;
                if(this.quit.signup) return this.quit.signup.user.profile.fullname;
                return '';
            },
            hasAccount(){
                if(!this.quit) return false;
                if(this.quit.account_number) return true;
                return false;
            }
            
        }, 
        methods: { 
            getDetails(quit){
                let withNumber=true;
                let html='';
                quit.details.forEach(item=>{
                    html += Quit.detailSummary(item,withNumber) + '<br>'
                })
                return html;
            },
            statusLable(){
              
                return Quit.statusLabel(this.quit.status);
            },
            editReview(){
               
                this.$emit('edit-status')
            },
            editPS(){
               
                this.$emit('edit-ps')
            }
            
        }
    }
</script>
