<template>
    <form v-if="form"  @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" style="font-size:16px">
        <teacher-inputs :form="form"></teacher-inputs>
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
import TeacherInputs from './inputs.vue'
export default {
	name:'EditTeacher',
	components: {
		'teacher-inputs':TeacherInputs
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
		
            let getData=Teacher.edit();

			getData.then(model => {
				let teacher=model.teacher;
				teacher.experiences=Helper.replaceAll(teacher.experiences,'<br>' , '\n')
				

				this.form = new Form({
					teacher:{
						...teacher
					}
				});

					
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		
		onSubmit(){
            this.submitting=true;
            let save=Teacher.update(this.form);

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

