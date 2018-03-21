<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<user-inputs :form="form">
		</user-inputs>
		
		
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
import UserInputs from './inputs.vue'
export default {
    name:'EditUser',
    props: {
        id: {
            type: String,
            default: ''
        },
	},
	components: {
		'user-inputs':UserInputs,
		
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
            let getData=User.edit(this.id);

			getData.then(model => {
				
				this.form = new Form({
					user:{
						...model.user
					},
					
				});
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
		onSubmit(){
           
            let save=User.update(this.id,this.form);

			save.then(user => {
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

