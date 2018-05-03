<template>
<div>
    <form v-if="form" :class="formStyle" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
		
		<admin-inputs v-if="id"  :form="form" :role_options="roleOptions" :center_options="centerOptions">
		</admin-inputs>
		<admin-create-inputs  v-else :form="form" :role_options="roleOptions" :center_options="centerOptions">
		</admin-create-inputs> 

		<submit-buttons :is_create="isCreate" :submitting="submitting"
		  @cancel="cancel">

		</submit-buttons>
		
		
    </form>
     <modal :showbtn="false"  :show.sync="userSelector.show"  @closed="userSelector.show=false" 
        effect="fade" :width="1200">
		<div slot="modal-header" class="modal-header modal-header-danger">
            
			<button id="close-button" type="button" class="close"  @click="userSelector.show=false">
					x
			</button>
			<h3 style="color:white">
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
				相同資料的使用者已經存在
			</h3>
		</div>
	
		<div slot="modal-body" class="modal-body">
			<user-selector v-if="userSelector.show" :model="userSelector.model"
             @selected="onExistUserSelected">
            </user-selector>
		</div>
    </modal>

</div>	

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

			userSelector:{
				model:null,
				show:false,
				user:null,
				
			},
			
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
							role:model.role,
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
							role:model.role,
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
		getErrors(){
			let errors={ };
			if(!this.form.user.phone) errors['user.phone']=['必須填寫手機'];

			if(!this.form.user.profile.fullname) errors['user.profile.fullname'] =['必須填寫姓名'];
			if(!this.form.user.profile.sid) errors['user.profile.sid'] =['必須填寫身分證號'];
		
			return errors;
		},
		onSubmit(){
			this.form.errors.clear();

			if(!this.isCreate){
				this.submit();
				return;
			}

			let errors=this.getErrors();
			if(!Helper.isEmptyObj(errors)){
				this.form.errors.record(errors);
				return;
			}


			let user=this.form.user;
			if(user.id){
				this.submit();
				return;
			}

			let find=User.find(user.email,user.phone,user.profile.sid);

			find.then(model => {
                    
					if(model.viewList.length){
						this.onExistUser(model);
					}else{
						this.submit();
					}
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		onExistUser(model){
			this.userSelector.model={
				...model
			};
			this.userSelector.show=true;
		},
		onExistUserSelected(id,user){
			
			let adminRole=user.roles.find(item=>{
				return item.name=='Boss' || item.name=='Staff' ;
			});
			if(adminRole){
				
				let url= Admin.source();
				let params={
					keyword:user.profile.fullname
				};
				window.location= Helper.buildQuery(url, params);
				return;
			}
			this.loadUser(user);
			
			this.userSelector.show=false;
			this.userSelector.model=null;
		},
		loadUser(user){
			
			this.form.user = {
				...user
			}; 

			
		},
		submit(){
			this.submitting=true;

			let save=null;
			
			if(this.isCreate) save=Admin.store(this.form); 
			else  save=Admin.update(this.id,this.form);

			save.then(admin => {
					this.submitting=false;
                    this.$emit('saved',admin);
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

