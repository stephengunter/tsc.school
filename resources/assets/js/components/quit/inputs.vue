<template>
    <div>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>日期</label>
                    <div>
                        <datetime-picker :date="form.quit.date" :can_clear="false" @selected="setDate"></datetime-picker>
                
                    </div>
                </div>
            </div>  
            
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>退款方式</label>
                    <drop-down :items="payway_options" :selected="form.quit.paywayId"
                        @selected="onPaywaySelected">
                    </drop-down>
                </div>
            </div>  
             
        </div>
        <div class="row" v-if="need_account">
            <div class="col-sm-3">
                <div class="form-group">                           
					<label>銀行名稱</label>
					<input type="text" name="quit.account_bank" class="form-control" v-model="form.quit.account_bank"  >
					<small class="text-danger" v-if="form.errors.has('quit.account_bank')" v-text="form.errors.get('quit.account_bank')"></small>
				</div>
            </div>  
            
            <div class="col-sm-3">
                <div class="form-group">
					<label>分行</label>
					<input type="text" name="quit.account_branch" class="form-control" v-model="form.quit.account_branch"  >
					<small class="text-danger" v-if="form.errors.has('quit.account_branch')" v-text="form.errors.get('quit.account_branch')"></small>
				</div>
            </div>  

            <div class="col-sm-3">
                <div class="form-group">
					<label>戶名</label>
					<input type="text" name="quit.account_owner" class="form-control" v-model="form.quit.account_owner" >
					<small class="text-danger" v-if="form.errors.has('quit.account_owner')" v-text="form.errors.get('quit.account_owner')"></small>
				</div>
            </div> 
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>銀行帳號</label>
                    <input type="text" name="quit.account_number" class="form-control" v-model="form.quit.account_number">
                    <small class="text-danger" v-if="form.errors.has('quit.account_number')" v-text="form.errors.get('quit.account_number')"></small>
                </div>
            </div>  
        </div>
        <div class="row">
            <div v-if="need_account" class="col-sm-3">
                <div class="form-group">
					<label>金資代碼</label>
					<input type="text" name="quit.account_code" class="form-control" v-model="form.quit.account_code" >
					<small class="text-danger" v-if="form.errors.has('quit.account_code')" v-text="form.errors.get('quit.account_code')"></small>
				</div>
            </div>  
            <div class="col-sm-9">
                <div class="form-group">                           
                    <label>備註</label>
                    <input type="text" name="quit.ps" class="form-control" v-model="form.quit.ps">
                    
                </div>
            </div>  
            
           
        </div> 
           
    </div>
</template>

<script>
export default {
    name:'QuitInputs',
    props: {
        form:{
            type: Object,
            default: null
        },
        payway_options:{
            type: Array,
            default: null
        }
	},
    data(){
		return {
			need_account:false
		}
	},
    computed:{
		
	},
    beforeMount() {
		this.setNeedAccount();
	},
    methods:{
		setNeedAccount(){
            if(!this.payway_options) return;

            let payway=this.payway_options.find(item=>{
                return item.value==this.form.quit.paywayId;
            });

            this.need_account=Helper.isTrue(payway.need_account);
        },
		setDate(val){
			this.form.quit.date=val;
        },
        onPaywaySelected(item){
            this.form.quit.paywayId=item.value;
            this.setNeedAccount();
        },
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

