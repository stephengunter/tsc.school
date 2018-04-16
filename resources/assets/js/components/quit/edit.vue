<template>
    <div>Quit
    <form v-if="false" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        <div class="row">
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>目標課程</label>
					<drop-down :items="courseOptions" :selected="form.quit.courseId"
                        @selected="onCourseSelected">
                    </drop-down>
				</div>
			</div>  
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>應繳/應退</label>
					<div>
						<toggle :items="isPayOptions"   :default_val="form.quit.isPay" @selected="setIsPay"></toggle>
					</div>
				</div>
			</div>  
            <div class="col-sm-4">
				<div class="form-group">                           
					<label>金額</label>
					<input type="text" name="quit.tuition" class="form-control" v-model="form.quit.tuition">
					<small class="text-danger" v-if="form.errors.has('quit.tuition')" v-text="form.errors.get('quit.tuition')"></small>
				</div>
			</div>  
		</div>
        <div class="row">
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>日期</label>
					<div>
						<datetime-picker :date="form.quit.date" :can_clear="false" @selected="setDate"></datetime-picker>
                
					</div>
				</div>
			</div> 
			<div class="col-sm-8">
				<div class="form-group">                           
					<label>備註</label>
					<input type="text" name="quit.ps" class="form-control" v-model="form.quit.ps">
					<small class="text-danger" v-if="form.errors.has('quit.ps')" v-text="form.errors.get('quit.ps')"></small>
				</div>
			</div>  
		</div>
		<div class="row">
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
    </div>
</template>

<script>


export default {
	name:'EditQuit',
    props: {
        id: {
            type: Number,
            default: 0
        },
        signup_id:{
            type: Number,
            default: 0
        }
	},
	data(){
		return {
			courseOptions:[],

			isPayOptions:[{
				text: '應繳',
				value: true
			}, {
				text: '應退',
				value: false
			}],

			student:null,

			form:null,
			
			isPay:true,
            
            submitting:false,
			
		}
	},
	computed:{
		isCreate(){
			return this.id < 1;
		}
	},
	beforeMount() {
		//this.init();
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
            if(this.isCreate){
                let params={
                    student:this.student_id
                };
                getData=Quit.create(params);
            }else{
                getData=Quit.edit(this.id);
            } 
			
			getData.then(model => {
				this.student={ ...model.student  };
				this.courseOptions=model.courseOptions.slice(0);


				this.form = new Form({
							quit:{
								...model.quit
							}
							
						});
				
				this.onDataFetched();
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		onDataFetched(){
			if(this.isCreate){
				let courseId=this.student.courseId;
				let index= this.courseOptions.findIndex((item)=>{
					return item.value==courseId;
				});
				if(index >= 0)  this.courseOptions.splice(index, 1); 

				this.form.quit.courseId=this.courseOptions[0].value;
			}
			
		},
		onCourseSelected(item){
			this.form.quit.courseId = item.value;
		},
		setIsPay(val){
			this.form.quit.isPay=val;
		},
		setDate(val){
			this.form.quit.date=val;
		},
		onSubmit(){
			this.submitting=true;

            let save=null;
            if(this.isCreate){
				
                save=Quit.store(this.form);
            }else{
                save=Quit.update(this.id,this.form);
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

