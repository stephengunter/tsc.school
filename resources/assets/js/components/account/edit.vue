<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<div class="row">
			<div class="col-sm-3">
				<div class="text-center">
					
					
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">                           
					<label>銀行名稱</label>
					<input type="text" name="account.bank" class="form-control" v-model="form.account.bank"  >
					<small class="text-danger" v-if="form.errors.has('account.bank')" v-text="form.errors.get('account.bank')"></small>
				</div>
				
				
				
			</div>
			<div class="col-sm-3">
				
				<div class="form-group">
					<label>分行</label>
					<input type="text" name="account.branch" class="form-control" v-model="form.account.branch"  >
					<small class="text-danger" v-if="form.errors.has('account.branch')" v-text="form.errors.get('account.branch')"></small>
				</div>
				
				
				
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					
					<label>戶名</label>
					<input type="text" name="account.owner" class="form-control" v-model="form.account.owner" >
					<small class="text-danger" v-if="form.errors.has('account.owner')" v-text="form.errors.get('account.owner')"></small>
				</div>
				
				
				
			</div>
            
        
    	</div>
		<div class="row">
			<div class="col-sm-3">
				
				
				
				
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					
					<label>帳號</label>
					<input type="text" name="account.number" class="form-control" v-model="form.account.number" >
					<small class="text-danger" v-if="form.errors.has('account.number')" v-text="form.errors.get('account.number')"></small>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					
					<label>金資代碼</label>
					<input type="text" name="account.code" class="form-control" v-model="form.account.code" >
					<small class="text-danger" v-if="form.errors.has('account.code')" v-text="form.errors.get('account.code')"></small>
				</div>
				
				
				
			</div>
            
        
    	</div>
		<div v-if="submitting"  class="row">
            <div class="col-sm-3">
            </div>
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
            <div class="col-sm-3">
            </div>
			<div class="col-sm-4">
				<div class="form-group">   
					<button type="submit" class="btn btn-success">
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
    name:'EditAccount',
    props: {
        id: {
            type: Number,
            default: 0
		},
		user_id:{
			type: Number,
			default: 0
		}
	},
	components: {
	
		
	},
	data(){
		return {

            form:null,
            
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
			this.fetchData();
		},
		cancel(){
			
			this.$emit('cancel');
		},
		fetchData(){
			let getData=null;
			if(this.id) getData=Account.edit(this.id);
			else getData=Account.create(this.user_id);

			getData.then(model => {
					
					this.form = new Form({
						account:{
							...model.account
						}
						
					});
					
				})
				.catch(error=> {
					Helper.BusEmitError(error); 
				})
            
        },
		onSubmit(){
            let save=null;
			if(this.id) save=Account.update(this.id,this.form);
			else save=Account.store(this.form);

			save.then(() => {
                    this.$emit('saved');
					Helper.BusEmitOK('資料已存檔');
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

