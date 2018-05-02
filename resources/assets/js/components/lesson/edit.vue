<template>
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
		<div class="row">	
			<div class="col-sm-3">
				<div class="form-group">                           
                	<label>課程</label>
					<input  class="form-control" readonly :value="form.lesson.course.fullName"/>
					
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">                           
                	<label>課堂日期</label>
					<div>
						<datetime-picker :date="form.lesson.date" @selected="setDate"></datetime-picker>
                		<small class="text-danger" v-if="form.errors.has('lesson.date')" v-text="form.errors.get('lesson.date')"></small>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group" >                           
                	<label>上課時間</label>
					<div class="form-inline" >
						<input type="text" name="lesson.on" class="form-control" v-model="form.lesson.on" style="width:35%">
                    	<small class="text-danger" v-if="form.errors.has('lesson.on')" v-text="form.errors.get('lesson.on')"></small>
							~
						<input type="text" name="lesson.off" class="form-control" v-model="form.lesson.off" style="width:35%">
						<small class="text-danger" v-if="form.errors.has('lesson.off')" v-text="form.errors.get('lesson.off')"></small>
					</div>
					
				</div>
			</div> 
			<div class="col-sm-3">
				<div class="form-group">                           
                	<label>上課地點</label>
					<input name="lesson.location" class="form-control" v-model="form.lesson.location"/>
					
				</div>
			</div>
		</div>	
		<div class="row">	
			<div class="col-sm-6">
				
				<div  class="form-group">  
					<label>授課教師</label>
					<v-select :value.sync="form.teacherIds" multiple  :options="teacherOptions" :onChange="onTeacherChanged" label="text">
						<slot name="no-options">-----</slot>
					</v-select>
					<small class="text-danger" v-if="!form.teacherIds.length" >請選擇教師</small>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">                           
                	<label>教育志工</label>
					<v-select :value.sync="form.volunteerIds" multiple  :options="volunteerOptions" :onChange="onVolunteerChanged" label="text">
						<slot name="no-options">-----</slot>
					</v-select>
				</div>
			</div>
			
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">                           
					<label>課程內容</label>
					<textarea rows="3"  class="form-control" name="lesson.content"  v-model="form.lesson.content">
					</textarea>
					
				</div>
			</div>
		</div> <!--  row   -->
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">                           
					<label>備註</label>
					<input type="text" name="lesson.ps" class="form-control" v-model="form.lesson.ps"  >
				</div>
			</div>
		
		</div> <!--  row   -->	
		<div v-if="submitting"  class="row">
			
			<div class="col-sm-3">
				<div class="form-group">                           
					<button class="btn btn-default">
                        <i class="fa fa-spinner fa-spin"></i> 
                        處理中
                    </button> 
				</div>
			</div>
    	</div>
		<div v-else class="row">
			
			<div class="col-sm-6">
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
    name:'EditLesson',
    props: {
        id: {
            type: Number,
            default: 0
		}
	},
	data(){
		return {
			teacherOptions:[],
			volunteerOptions:[],

            form :null,
            
			submitting:false,
			
		}
	},
	computed:{
		

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
            let getData=Lesson.edit(this.id)

			getData.then(model => {
				this.form = new Form({
							lesson:{
								...model.lesson
							},
							teacherIds:model.teacherIds.slice(0),
							volunteerIds:model.volunteerIds.slice(0)
						});

				
				
				this.teacherOptions=model.teacherOptions.slice(0);
				this.volunteerOptions=model.volunteerOptions.slice(0);
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		setDate(val){
			this.form.lesson.date=val;
			this.clearErrorMsg('lesson.date');
		},
		onTeacherChanged(val){
            if(val.length) this.clearErrorMsg('teacherIds');
		},
		onVolunteerChanged(val){
			
		},
		onSubmit(){
			let save=Lesson.update(this.id,this.form);

			save.then(lesson => {
                    this.$emit('saved',lesson);
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

