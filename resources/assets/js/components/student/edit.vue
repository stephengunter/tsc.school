<template>
    
    <form v-if="form" class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
        <div class="row">
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>分數</label>
					<input type="text" name="student.score" class="form-control" v-model="form.student.score">
					<small class="text-danger" v-if="form.errors.has('student.score')" v-text="form.errors.get('student.score')"></small>
				</div>
			</div>  
			<div class="col-sm-8">
				<div class="form-group">                           
					<label>備註</label>
					<input type="text" name="student.ps" class="form-control" v-model="form.student.ps">
					<small class="text-danger" v-if="form.errors.has('student.ps')" v-text="form.errors.get('student.ps')"></small>
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
	name:'EditStudent',
	
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
            let getData=Student.edit(this.id);
			
			getData.then(model => {
				if(!Helper.tryParseInt(model.student.score)) model.student.score='';
				this.form = new Form({
							student:{
								...model.student
							}
							
						});
				
				
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
		onSubmit(){
			this.submitting=true;

			let save=Student.update(this.id,this.form);
          

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

