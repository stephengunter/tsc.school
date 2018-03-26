<template>
    
    <form v-if="form" :class="formStyle" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<teacher-inputs v-if="id" :group="group"  :form="form" :centers="centerOptions">
		</teacher-inputs>
		<teacher-create-inputs  v-else :form="form" :group="group" :centers="centerOptions">
		</teacher-create-inputs> 
		
		
		<submit-buttons :is_create="isCreate" :submitting="submitting"
		  @cancel="cancel">

		</submit-buttons>
    </form>
    
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
        setOpenDate(val){
            this.form.teacher.openDate=val;
        },
        setBirdDate(val){
            this.form.teacher.birdDate=val;
        },
        setCloseDate(val){
            this.form.teacher.closeDate=val;
        },
		onSubmit(){
			this.submitting=true;

			let save=null;
			if(this.group) {
				if(this.id) save=TeacherGroup.update(this.id,this.form);
            	else  save=TeacherGroup.store(this.form); 
			}else{

				
				if(this.id) save=Teacher.update(this.id,this.form);
           	    else{
					save=Teacher.store(this.form);
					this.form.teacher.user={...this.form.user } ;
				} 
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

