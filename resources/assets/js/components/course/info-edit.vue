<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        
		<div class="row">
                    
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>人數上限</label>
                    <input type="text" name="course.limit" class="form-control" v-model="form.course.limit">
                    <small class="text-danger" v-if="form.errors.has('course.limit')" v-text="form.errors.get('course.limit')"></small>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>最低人數</label>
                    <input type="text" name="course.min" class="form-control" v-model="form.course.min">
                    <small class="text-danger" v-if="form.errors.has('course.min')" v-text="form.errors.get('course.min')"></small>
                </div>
                
            </div>
            <div class="col-sm-3">
                
            </div>
            <div class="col-sm-3">
                
            </div>
        </div>
            <div class="row">
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>學費</label>
                    <input type="text" name="course.tuition" class="form-control" v-model="form.course.tuition">
                    <small class="text-danger" v-if="form.errors.has('course.tuition')" v-text="form.errors.get('course.tuition')"></small>
                </div>
            </div>
            
            <div class="col-sm-3">
                <div class="form-group">                           
                    <label>教材費用</label>
                    <input type="text" name="course.cost" class="form-control" v-model="form.course.cost">
                    <small class="text-danger" v-if="form.errors.has('course.cost')" v-text="form.errors.get('course.cost')"></small>
                </div>
            </div>
            <div class="col-sm-6">
                
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">                           
                    <label>教材費用說明</label>
                    <textarea rows="4" cols="50" class="form-control" name="course.materials"  v-model="form.course.materials">
                    </textarea>
                    <small class="text-danger" v-if="form.errors.has('course.materials')" v-text="form.errors.get('course.materials')"></small>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">                           
                    <label>注意事項</label>
                    <textarea rows="4" cols="50" class="form-control" name="course.caution"  v-model="form.course.caution">
                    </textarea>
                    <small class="text-danger" v-if="form.errors.has('course.caution')" v-text="form.errors.get('course.caution')"></small>
                </div>
            </div>
            
        </div>
		
		<div v-if="submitting"  class="row">
			
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
export default {
    name:'EditCourseInfo',
    props: {
        id: {
            type: Number,
            default: 0
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
		fetchData(){
            let getData=Course.editInfo(this.id);
			
			getData.then(model => {
				
				this.form = new Form({
					course:{
                        ...model.course,
                        tuition:Helper.formatMoney(model.course.tuition),
                        cost:Helper.formatMoney(model.course.cost),
					}
				});

				
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
        cancel(){
			this.$emit('cancel');
		},
		onSubmit(){
			
			this.submitting=true;

			let save=Course.updateInfo(this.id,this.form);

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

