<template>
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>日期</label>
                    <input type="text" name="quit.date" class="form-control" :value="form.quit.date"  readonly>
                </div>
            </div>  
            
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>退款方式</label>
                    <input type="text" name="quit.date" class="form-control" :value="payway.name"  readonly>
                </div>
            </div>  
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>退還學費</label>
                    <input type="text" name="quit.date" class="form-control" :value="form.quit.tuitions"  readonly>
                </div>
            </div> 
           
        </div>
        <div class="row">
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
            <div  class="col-sm-3">
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
        <div v-if="submitting"  class="row">
            <div class="col-sm-4">
                <div class="form-group">                           
                    <button class="btn btn-default">
                        <i class="fa fa-spinner fa-spin"></i> 
                        處理中
                    </button> 
                </div>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-sm-12">
                <div class="form-group">   
                    <button type="submit" class="btn btn-success" >
                        <i class="fa fa-save"></i>
                        確認存檔
                    </button> 
                   
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-default" @click.prevent="cancel">
                        取消
                    </button>  
                </div>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    name:'EditQuit',
    props: {
        tran:{
            type: Object,
            default: null
        }
	},
    data(){
		return {
			
            
            form:null,
            
            //quitDetails:[],

            payway:null,
            
            submitting:false,
			
		}
	},
    computed:{
        
	},
    beforeMount() {
		this.init();
	},
    methods:{
		init(){
            let params={ 
                    tran: this.tran.id 
                };
            let fetchData= Tran.createQuit(params);
            
            fetchData.then(data => {
                    this.form = new Form({
                        quit:{
                            ...data.quit
                        },
                        details:[]
                    })
                    //this.quitDetails=data.details.slice(0);

                    this.payway={...data.payway };
				})
				.catch(error => {
					
					Helper.BusEmitError(error);
				})

        },
        cancel(){
			this.$emit('cancel');
        },
        onSubmit(){

            this.submitQuit();
        },
        submitQuit(){
            let save=Tran.storeQuit(this.form);
         
			save.then(() => {
					this.submitting=false;
                    this.$emit('saved');
					Helper.BusEmitOK('資料已存檔');
				})
				.catch(error => {
					this.submitting=false;
					Helper.BusEmitError(error,'存檔失敗');
				})
        },
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

