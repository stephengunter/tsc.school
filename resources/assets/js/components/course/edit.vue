<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<course-inputs :form="form" :centers="centerOptions" :terms="termOptions" 
			:categories="categoryOptions" :teachers="teacherOptions">
		</course-inputs>
		
		
		<div v-if="submitting"  class="row">
			<div v-if="id"  class="col-sm-3">
            </div>
			<div class="col-sm-4">
				<div class="form-group">                           
					<button class="btn btn-default">
                         <i class="fa fa-spinner fa-spin"></i> 
                         處理中
                    </button> 
				</div>
			</div>
    	</div>
		<div v-else class="row">
			<div v-if="id" class="col-sm-3">
            </div>
			<div class="col-sm-4">
				<div class="form-group">   
					<button type="submit" class="btn btn-success" >
						<i class="fa fa-save"></i>
						確認存檔
					</button> 
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="button" class="btn btn-default" @click.prevent="cancel">
						取消
					</button>  
				</div>
			</div>
    	</div>
    </form>
    
</template>

<script>
import courseInputs from './inputs.vue'
export default {
    name:'Editcourse',
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
	components: {
		'course-inputs':courseInputs,
		
	},
	data(){
		return {

			centerOptions:[],
			termOptions:[],
			categoryOptions:[],
			teacherOptions:[],

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
            if(this.id)  getData=Course.edit(this.id);
			else getData=Course.create();
			
			getData.then(model => {
				
				this.form = new Form({
					course:{
						...model.course
					},
					teacherIds:model.teacherIds.slice(0)
				});

				this.centerOptions=model.centerOptions.slice(0);
				this.termOptions=model.termOptions.slice(0);
				this.categoryOptions=model.categoryOptions.slice(0);
				this.teacherOptions=model.teacherOptions.slice(0);
				
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
        setActive(val) {
            this.form.course.active = val;
        },
        setOpenDate(val){
            this.form.course.openDate=val;
        },
        setBirdDate(val){
            this.form.course.birdDate=val;
        },
        setCloseDate(val){
            this.form.course.closeDate=val;
        },
		onSubmit(){
			
			this.submitting=true;

			let save=null;
			if(this.id) save = Course.update(this.id,this.form);
           	else  save = Course.store(this.form); 
          

			save.then((course) => {
					this.submitting=false;
					if(this.id)  this.$emit('saved');
					else  this.$emit('saved',course);
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

