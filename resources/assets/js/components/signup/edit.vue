<template>
    <div>
		<form v-if="form" :class="formStyle" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
			
			
			<signup-create-inputs  :form="form" :identity_options="identityOptions" 
				@add-detail="onAddDetail" @reset-user="resetUser" @user-saved="onUserSaved">
			</signup-create-inputs> 

			<submit-buttons :form="form"  :submitting="submitting"
			@cancel="cancel">

			</submit-buttons>
			
			
		</form>

		<modal :showbtn="false"  :show.sync="courseSelector.show"  @closed="courseSelector.show=false" 
			effect="fade" :width="1200">
			<div slot="modal-body" class="modal-body">
				<course-selector v-if="courseSelector.show" :centers="centerOptions" :center_id="centerId"
				   @submit="onSubmitCoursesToAdd">
				</course-selector>
			</div>
		</modal>
    </div>
</template>

<script>
import SignupCreateInputs from './create-inputs.vue';
import SubmitButtons from './submit-buttons.vue';
import CourseSelector from './course-selector.vue'

export default {
    name:'EditSignup',
    props: {
        id: {
            type: Number,
            default: 0
		},
		params: {
            type:Object,
            default: null
		}
	},
	components: {
		'signup-create-inputs':SignupCreateInputs,
		'submit-buttons':SubmitButtons,
		'course-selector':CourseSelector
	},
	data(){
		return {
			centerId:0,
			centerOptions:[],
			courseOptions:[],
			identityOptions:[],

            courseSelector:{
				show:false,
				selected:0,
			},

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
            if(this.isCreate) getData=Signup.create(this.params);
            else  getData=Signup.edit(this.id);

			getData.then(model => {
				if(this.isCreate){
					this.form = new Form({
							signup:{
								...model.signup
							},
							user:{
								...model.user
							},
							lotus:model.lotus,
							courseIds:model.courseIds.slice(0),
							identityIds:model.identityIds.slice(0)
						});

					this.courseOptions=model.courseOptions.slice(0);	
					if(model.courseOptions.length){
						this.courseSelector.selected=model.courseOptions[0].value;
					}

					this.identityOptions=model.identityOptions.slice(0);

					this.centerOptions=model.centerOptions.slice(0);
					this.centerId=model.centerId;
					
				}else{
					this.form = new Form({
						signup:{
							...model.signup
						},
						
					});
				}
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		setUser(user){
			this.form.user={...user };
		},
		resetUser(){
			this.form.user={
				id:0,
				email:'',
				phone:'',
				profile:{
					dob:'',
					sid:'',
					fullname:'',
					gender:1
				}

			};
		},
		onUserSaved(id){
		
			this.$emit('user-saved',id);
		},
		onAddDetail(){
			this.courseSelector.show=true;
		},
		onSubmitCoursesToAdd(courses){
			this.courseSelector.show=false;
			courses.forEach(course=>{
				this.addDetail(course);
			});
			
		},
		addDetail(course){
			
			let exist=this.form.courseIds.includes(course.id);
			if(exist) return;

			let detail={
				id:0,
				courseId:course.id,
				tuition:course.tuition,
				cost:course.cost,
				course:course
			};
			this.form.courseIds.push(course.id);
			this.form.signup.details.push(detail);

			this.clearErrorMsg('courseIds');
		},
		onSubmit(){
			if(!this.isCreate){
				this.submit();
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

			this.form.signup.user= { ...this.form.user };
			
			if(this.isCreate) save=Signup.store(this.form); 
			else  save=Signup.update(this.id,this.form);

			save.then(signup => {
                    this.$emit('saved',signup);
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

