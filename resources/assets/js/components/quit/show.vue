<template>
<div v-if="quit">
    <div class="show-data">
        <div class="row" >
            <div  class="col-sm-4">
                <label class="label-title">應退金額</label>
                <p>
                    {{ quit.amount | formatMoney  }}
                </p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">資料審核</label>
                <p>
                    <span v-html="$options.filters.reviewedLabel(quit.reviewed)"></span>
                    &nbsp;
                    <button class="btn btn-primary btn-xs" @click.prevent="editReview">
                        <i class="fa fa-edit"></i>
                    </button>
                </p>                   
            </div>
            <div  class="col-sm-4">
                <label class="label-title">狀態</label>
                <p v-html="$options.filters.quitStatusLabel(quit.status)"></p>                      
            </div>
        </div>  <!-- End row--> 
        
    </div>
    <quit-details :quit_details="quit.details" :can_edit="false"  >
       
    </quit-details>
    <div class="show-data">
        <div class="row" >
            <div  class="col-sm-4">
                <label class="label-title">申請日期</label>
                <p v-text="quit.date"></p>                      
            </div>
            <div class="col-sm-4">
                <label class="label-title">手續費</label>
                <p >
                     {{ quit.fee | formatMoney  }}
                </p>                   
            </div>
            <div class="col-sm-4">
                <label class="label-title">退款方式</label>
                <p>
                   {{ quit.payway.name }}
                </p>    
            </div>
        </div>  <!-- End row--> 
        
        
        <div  class="row">
            <div class="col-sm-4">
                <label class="label-title">銀行帳號</label>
                <p >
                   {{ quit.account }}
                </p>                      
            </div>
            <div class="col-sm-8">
                <label class="label-title">備註</label>
                <p v-text="quit.ps"></p>                      
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
            }
            
        }, 
        methods: { 
            statusLable(){
              
                return Quit.statusLabel(this.quit.status);
            },
            editReview(){
                this.$emit('edit-review')
            }
            
        }
    }
</script>
