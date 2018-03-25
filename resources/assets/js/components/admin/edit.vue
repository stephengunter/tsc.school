<template>
    
    <form v-if="form" :class="formStyle" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
		
		<admin-inputs v-if="id"  :form="form" :role_options="roleOptions" :center_options="centerOptions">
		</admin-inputs>
		<admin-create-inputs  v-else :form="form" :role_options="roleOptions" :center_options="centerOptions">
		</admin-create-inputs> 

		<submit-buttons :is_create="isCreate" :submitting="submitting"
		  @cancel="cancel">

		</submit-buttons>
		
		
    </form>
    
</template>

<script>
import AdminInputs from './inputs.vue';
import AdminCreateInputs from './create-inputs.vue';
import SubmitButtons from './submit-buttons.vue';

export default {
    name:'EditAdmin',
    props: {
        id: {
            type: Number,
            default: 0
		},
		user:{
			type: Object,
            default: null
		}
	},
	components: {
		'admin-inputs':AdminInputs,
		'admin-create-inputs':AdminCreateInputs,
		'submit-buttons':SubmitButtons
	},
	data(){
		return {
			roleOptions:[],
			centerOptions:[],

            

            form :null,
            
			submitting:false,
			
		}
	},
	computed:{
		isCreate(){
			return this.id < 1;
		},
		formStyle(){
			if(this.isCreate) return 'form-horizontal';
			return 'form';
		},

	},
	beforeMount() {
		this.init();
	}, 
	methods:{
		
		cancel(){
			
			this.$emit('cancel');
		},
		init(){
			this.fetchData();
		},
		fetchData(){
            let getData=null;
            if(this.isCreate) getData=Admin.create();
            else  getData=Admin.edit(this.id);

			getData.then(model => {
				if(this.isCreate){
					if(this.user){
						this.form = new Form({
							admin:{
								...model.admin
							},
							user:{
								...this.user
							},
							centerIds:[]
						});


					}else{
						this.form = new Form({
							admin:{
								...model.admin
							},
							user:{
								...model.user
							},
							centerIds:[]
						});
					}
					
				}else{
					this.form = new Form({
						admin:{
							...model.admin
						},
						role:model.role,
						centerIds:[]
					});
				}
				
				this.roleOptions = model.roleOptions.slice(0);

				

				if(model.centerOptions){
					this.centerOptions=model.centerOptions.slice(0);
					
					this.form.centerIds=model.centerIds.map((id)=>{
						return parseInt(id);
					});
				}
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
        setActive(val) {
            this.form.admin.active = val;
        },
        setOpenDate(val){
            this.form.admin.openDate=val;
        },
        setBirdDate(val){
            this.form.admin.birdDate=val;
        },
        setCloseDate(val){
            this.form.admin.closeDate=val;
        },
		onSubmit(){

			if(!this.isCreate){
				this.submit();
				return;
			}

			let user=this.form.user;

			let find=User.find(user.email,user.phone,user.profile.sid);

			find.then(model => {
                    if(model.viewList.length){
						this.$emit('exist-user',model);
					}else{
						this.submit();
					}
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		submit(){
			let save=null;
			
			if(this.isCreate) save=Admin.store(this.form); 
			else  save=Admin.update(this.id,this.form);

			save.then(admin => {
                    this.$emit('saved',admin);
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

