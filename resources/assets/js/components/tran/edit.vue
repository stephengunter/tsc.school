<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        <div class="row">
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>目標課程</label>
					<drop-down :items="courseOptions" :selected="form.tran.courseId"
                        @selected="onCourseSelected">
                    </drop-down>
				</div>
			</div>  
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>應繳/應退</label>
					<div>
						<toggle :items="isPayOptions"   :default_val="form.tran.isPay" @selected="setIsPay"></toggle>
					</div>
				</div>
			</div>  
            <div class="col-sm-4">
				<div class="form-group">                           
					<label>金額</label>
					<input type="text" name="tran.tuition" class="form-control" v-model="form.tran.tuition">
					<small class="text-danger" v-if="form.errors.has('tran.tuition')" v-text="form.errors.get('tran.tuition')"></small>
				</div>
			</div>  
		</div>
        <div class="row">
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>日期</label>
					<div>
						<datetime-picker :date="form.tran.date" :can_clear="false" @selected="setDate"></datetime-picker>
                
					</div>
				</div>
			</div> 
			<div class="col-sm-8">
				<div class="form-group">                           
					<label>備註</label>
					<input type="text" name="tran.ps" class="form-control" v-model="form.tran.ps">
					<small class="text-danger" v-if="form.errors.has('tran.ps')" v-text="form.errors.get('tran.ps')"></small>
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
    
</template>

<script>


export default {
	name:'EditTran',
    props: {
        id: {
            type: Number,
            default: 0
        },
        student_id:{
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
            if(this.isCreate){
                let params={
                    student:this.student_id
                };
                getData=Tran.create(params);
            }else{
                getData=Tran.edit(this.id);
            } 
			
			getData.then(model => {
				this.student={ ...model.student  };
				this.courseOptions=model.courseOptions.slice(0);


				this.form = new Form({
							tran:{
								...model.tran
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

				this.form.tran.courseId=this.courseOptions[0].value;
			}
			
		},
		onCourseSelected(item){
			this.form.tran.courseId = item.value;
		},
		setIsPay(val){
			this.form.tran.isPay=val;
		},
		setDate(val){
			this.form.tran.date=val;
		},
		onSubmit(){
			this.submitting=true;

            let save=null;
            if(this.isCreate){
				
                save=Tran.store(this.form);
            }else{
                save=Tran.update(this.id,this.form);
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

