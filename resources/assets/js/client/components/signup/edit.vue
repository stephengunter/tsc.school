<template>
    <form v-if="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
		<div class="field">
			
			<signup-create-inputs  :form="form" :identity_options="identity_options" >
			
			</signup-create-inputs> 

			<submit-buttons :form="form"  :submitting="submitting"
			@cancel="cancel">

			</submit-buttons>

		</div>	
		
	</form>
</template>

<script>
import SignupCreateInputs from './create-inputs.vue';
import SubmitButtons from './submit-buttons.vue'

export default {
    name:'EditSignup',
    props: {
        form: {
            type: Object,
            default: null
		},
		identity_options: {
            type: Array,
            default: null
		},
	},
	components: {
		'signup-create-inputs':SignupCreateInputs,
		'submit-buttons':SubmitButtons
	},
	data(){
		return {
            
			submitting:false,
			
		}
	},
	computed:{
		isCreate(){
			if(!this.form) return true;
			if(!this.form.signup.id) return true;
			return  parseInt(this.form.signup.id) < 1 ;
		}
	},
	beforeMount() {
		
	}, 
	methods:{
		cancel(){
			this.$emit('cancel');
		},
		init(){
			this.fetchData();
		},
		onSubmit(){
			this.submitting=true;
			let save=null;

			this.form.signup.user= { ...this.form.user };

		
			
			if(this.isCreate) save=Signup.store(this.form); 
			else  save=Signup.update(this.id,this.form);

			save.then(() => {
					this.submitting=false;
                    this.$emit('saved');
					Bus.$emit('okmsg','報名資料已存檔');
				})
				.catch(error => {
					this.submitting=false;
					Bus.$emit('errors','報名資料存檔失敗');
				})
			
		},
		
		clearErrorMsg(name) {
			
      	    this.form.errors.clear(name);
        },
	}
}
</script>

