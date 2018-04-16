<template>
<div>
    <form v-if="form" :class="formStyle" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<teacher-inputs v-if="id" :group="group"  :form="form" :centers="centerOptions">
		</teacher-inputs>
		<teacher-create-inputs  v-else :form="form" :group="group" :centers="centerOptions">
		</teacher-create-inputs> 
		
		
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

import TeacherCreateInputs from './create-inputs.vue';
import TeacherInputs from './inputs.vue';
import SubmitButtons from './submit-buttons.vue';

export default {
	name:'EditTeacher',
	components: {
		
		'teacher-create-inputs':TeacherCreateInputs,
		'teacher-inputs':TeacherInputs,
		'submit-buttons':SubmitButtons
    },
    props: {
        id: {
            type: Number,
            default: 0
		},
		group:{
			type: Boolean,
			default: false
		}, 
	},
	data(){
		return {

            centerOptions:[],

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
		onCentersChanged(values){
			this.form.centerIds=values.slice(0);
			
		},
		cancel(){
			
			this.$emit('cancel');
		},
		init(){
			this.fetchData();
		},
		fetchData(){
            let getData=null;
            if(this.group) {
				if(this.id) getData=TeacherGroup.edit(this.id);
				else getData=TeacherGroup.create();
			}else{
				if(this.id)  getData=Teacher.edit(this.id);
				else getData=Teacher.create();
			}
			
			getData.then(model => {

				if(this.group){

					this.centerOptions=model.centerOptions.slice(0);
					this.form = new Form({
									teacher:{
										...model.teacher
									},
									
								});

				}else{
					if(this.id){
						this.form = new Form({
							teacher:{
								...model.teacher
							},
							
							centerIds:[]
						});
					}else{
						this.form = new Form({
							teacher:{
								...model.teacher
							},
							user:{
								...model.user
							},
							centerIds:[]
						});
					}
					
					this.form.teacher.experiences=Helper.replaceAll(this.form.teacher.experiences,'<br>' , '\n')
					
					this.form.teacher.wage=Helper.formatMoney(this.form.teacher.wage);
					
				}

				if(model.centerOptions){
					this.centerOptions=model.centerOptions.slice(0);
					
					if(model.centerIds){
						this.form.centerIds=model.centerIds.map((id)=>{
							return parseInt(id);
						});
					}
					
				}
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
        setActive(val) {
            this.form.teacher.active = val;
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

			if(this.group){
				this.submitGroupTeacher();
				return;
			}  
				
			if(this.id){
				this.submitTeacher();
				return;
			} 

			let errors=this.getErrors();
			if(!Helper.isEmptyObj(errors)){
				this.form.errors.record(errors);
				return;
			}
			

			let user=this.form.user;
			if(user.id){
				this.submitTeacher();
				return;
			}
			let find=User.find(user.email,user.phone,user.profile.sid);

			

			find.then(model => {
                    if(model.viewList.length){
						this.onExistUser(model);
					}else{
						this.submitTeacher();
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
			
			let teacherRole=user.roles.find(item=>{
				return item.name=='Teacher';
			});
			if(teacherRole){
				
				let url= Teacher.source();
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
		submitGroupTeacher(){
			this.submitting=true;

			let save=null;
			if(this.id) save=TeacherGroup.update(this.id,this.form);
            else  save=TeacherGroup.store(this.form); 

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
		submitTeacher(){
			this.submitting=true;

			let save=null;
			if(this.id) save=Teacher.update(this.id,this.form);
			else{
				save=Teacher.store(this.form);
				this.form.teacher.user={...this.form.user } ;
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

