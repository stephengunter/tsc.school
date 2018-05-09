<template>
    <div>
		<form v-if="form" :class="formStyle" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
			
			<volunteer-inputs v-if="id"  :form="form">
			</volunteer-inputs>
			<volunteer-create-inputs  v-else :form="form" >
			</volunteer-create-inputs> 
			
			
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

import VolunteerCreateInputs from './create-inputs.vue';
import VolunteerInputs from './inputs.vue';
import SubmitButtons from './submit-buttons.vue';

export default {
	name:'EditVolunteer',
	components: {
		
		'volunteer-create-inputs':VolunteerCreateInputs,
		'volunteer-inputs':VolunteerInputs,
		'submit-buttons':SubmitButtons
    },
    props: {
        id: {
            type: Number,
            default: 0
		},
	},
	data(){
		return {

            form:null,
            
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
            if(this.id)  getData=Volunteer.edit(this.id);
			else getData=Volunteer.create();
			
			getData.then(model => {

				if(this.id){
					this.form = new Form({
						volunteer:{
							...model.volunteer
						},
						
						
					});
				}else{
					this.form = new Form({
						volunteer:{
							...model.volunteer
						},
						user:{
							...model.user
						}
					});
				}
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		getErrors(){
			let errors={ };
			// if(!this.form.user.phone) errors['user.phone']=['必須填寫手機'];

			if(!this.form.user.profile.sid) errors['user.profile.sid'] =['必須填寫身分證號'];
			
		
			return errors;
		},
		onExistUser(model){
		
			this.userSelector.model={
				...model
			};
			this.userSelector.show=true;
		},
		onExistUserSelected(id,user){
			
			let volunteerRole=user.roles.find(item=>{
				return item.name=='Volunteer';
			});
			if(volunteerRole){
				
				let url= Volunteer.source();
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
		onSubmit(){
			this.form.errors.clear();
			if(this.id){
				this.submitVolunteer();
				return;
			} 

			let errors=this.getErrors();
			if(!Helper.isEmptyObj(errors)){
				this.form.errors.record(errors);
				return;
			}

			let user=this.form.user;
			if(user.id){
				this.submitVolunteer();
				return;
			}
			let find=User.find(user.email,user.phone,user.profile.sid);

			

			find.then(model => {
                    if(model.viewList.length){
						this.onExistUser(model);
					}else{
						this.submitVolunteer();
					}
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		submitVolunteer(){
			this.submitting=true;
			let save=null;
			if(this.id) save=Volunteer.update(this.id,this.form);
			else{
				save=Volunteer.store(this.form);
				this.form.volunteer.user={...this.form.user } ;
			} 
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

