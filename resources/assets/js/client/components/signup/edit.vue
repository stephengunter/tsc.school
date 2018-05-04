<template>
    <form v-if="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
		<div class="field">
			
			<signup-edit-inputs  :form="form" :identity_options="identity_options"
				@remove-detail="onRemoveDetail" >
			
			</signup-edit-inputs> 

			<submit-buttons :form="form"  :submitting="submitting"
				@cancel="cancel">
			</submit-buttons>

			<div class="control" style="padding-top:1em">
				<discount-view :center="center" :bird_date_text="bird_date_text"></discount-view>
			</div>

		</div>	
		
	</form>
</template>

<script>
import SignupEditInputs from './edit-inputs.vue';
import SubmitButtons from './submit-buttons.vue';
import DiscountView from '../discount/view.vue';

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
		center:{
			type: Object,
            default: null	
		},
		bird_date_text: {
			type: String,
			default: ''
		},
	},
	components: {
		'signup-edit-inputs':SignupEditInputs,
		'submit-buttons':SubmitButtons,
		'discount-view':DiscountView     
	},
	data(){
		return {
            
			submitting:false,
			
		}
	},
	computed:{
		isCreate(){
		
			return  this.getId() < 1 ;
		}
	},
	beforeMount() {
		
	}, 
	methods:{
		getId(){
			if(!this.form) return 0;
			if(!this.form.signup) return 0;
		    if(!this.form.signup.id) return 0;
			return  parseInt(this.form.signup.id) ;
		},
		cancel(){
			this.$emit('cancel');
		},
		init(){
			this.fetchData();
		},
		onRemoveDetail(item){
			let courseId=item.courseId;
            let index=this.form.signup.details.findIndex((detail) =>{
                return detail.courseId==courseId;
            });
            this.form.signup.details.splice(index, 1);
		},
		onSubmit(){
			this.submitting=true;
			let save=null;

			this.form.signup.user= { ...this.form.user };
			
			if(this.isCreate) save=Signup.store(this.form); 
			else  save=Signup.update(this.getId(),this.form);

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

