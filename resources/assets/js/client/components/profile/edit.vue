<template>
    <form v-if="form"  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" style="font-size:16px">
        <profile-inputs :form="form"></profile-inputs>
        <div class="columns">
            <div v-if="submitting"  class="column" >
                <button type="submit" class="button is-outlined">
					 <i class="fa fa-spinner fa-spin"></i> &nbsp;處理中
                    
                </button>
                
                
            </div>
            <div v-else class="column" >
                <button type="submit" class="button is-primary" >確定送出</button>
                &nbsp;
                <a @click.prevent="cancel" class="button is-outlined">取消</a>
                
            </div>
        </div>  
    </form>
    
    
</template>

<script>
import ProfileInputs from './inputs.vue'
export default {
	name:'EditProfile',
	components: {
		'profile-inputs':ProfileInputs
	},
    props: {
        id: {
            type: Number,
            default: 0
		},
		role:{
			type: String,
            default: ''	
		}
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
					role:this.role
					
				});
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		setGender(val){
			this.form.user.profile.gender=val;
		},
		setDOB(val){
			this.form.user.profile.dob=val;
			this.clearErrorMsg('user.profile.dob');
		},
		onSubmit(){
            this.submitting=true;
            let save=User.update(this.form);

			save.then(user => {
					this.submitting=false;
					this.$emit('saved');
					Bus.$emit('okmsg','資料已存檔');
				})
				.catch(error => {
					this.submitting=false;
					Bus.$emit('errors','存檔失敗');
				})
			
		},
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

