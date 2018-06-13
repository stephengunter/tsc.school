<template>

<div>    
    <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
		
		<div class="form-group">
			<label class="col-md-2 control-label">名稱</label>
			<div class="col-md-10">
				<input name="title" v-model="doc.title" class="form-control" />
				<small class="text-danger" v-if="titleError" v-text="titleError"></small>
			
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-md-2 control-label">檔案</label>
			<div v-if="doc.name" class="col-md-4">
				<input name="name" readonly :value="doc.name" class="form-control" />
			</div>
			<div class="col-md-4">
				<small class="text-danger" v-if="err_msg" v-text="err_msg"></small>
				<label  :disabled="submitting" class="btn  btn-primary btn-file" @click="initFile">
					<i class="fa fa-upload"></i>
						上傳
					<input :disabled="submitting" type="file"  ref="fileinput"  name="file" style="display: none;"  
					@change="onFileChange" >
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="col-md-2 control-label"></label>
			
			<div v-if="submitting"  class="col-md-10">
				<button class="btn btn-default">
					<i class="fa fa-spinner fa-spin"></i> 
					處理中
				</button>
			</div>
			<div v-else class="col-md-10">
				<button class="btn btn-success" type="submit">
					<i class="fa fa-floppy-o" aria-hidden="true"></i>
						確認存檔
				</button>
				
				
				&nbsp;&nbsp;&nbsp;
				<button @click.prevent="cancel" class="btn btn-default">
					
					取消
				</button>
			</div>
		</div>
		
	</form>
</div>    
</template>

<script>
export default {
    name:'EditDoc',
    props: {
        id: {
            type: Number,
            default: 0
        },
	},
	data(){
		return {

			doc:{
				id:0,
				title:'',
				name:'',
			},

			titleError:'',
			
			files: [],

			err_msg:'',

			submitting:false,
			
		}
	},
	computed:{
		isCreate(){
			return this.id == 0;
		}
	},
	beforeMount() {
		this.init();
	}, 
	methods:{
		init(){
			if(this.id) this.fetchData();
			
		},
		initFile(){
			this.$refs.fileinput.value = null;
			this.err_msg='';
		},
		cancel(){
			this.$emit('cancel');
		},
		fetchData(){
			let getData=Doc.edit(this.id);

			getData.then(model => {
				
				this.doc = {
					...model.doc
				};

				this.$emit('loaded',model.doc);
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
		},
		onFileChange(e) {
               
			var files = e.target.files || e.dataTransfer.files;
			if (!files.length)  return;

			this.doc.name = files[0].name;
				
			this.files = e.target.files;
		},
		canSubmit(){
			if(this.titleError) return false;
			if(this.err_msg) return false;

			return true;
		},
		onSubmit(){

			if(!this.doc.title) this.titleError='請填寫文件名稱';
			if(!this.doc.name) this.err_msg='無法取得上傳檔案';

			if(!this.canSubmit()) return;

			this.submitting=true;

			let form = new FormData();

			if(this.files.length){
				for (let i = 0; i < this.files.length; i++) {
                    form.append('file', this.files[i]);                    
                }
			}

			form.append('id', this.doc.id);
			form.append('title', this.doc.title);
			form.append('name', this.doc.name);

			let save=Doc.store(form);

			save.then(() => {
					this.submitting=false;
                    this.$emit('saved');
					Helper.BusEmitOK('資料已存檔');
					
				})
				.catch(error => {
					let msg =error.response.data.errors.msg[0];
					if(msg){
						this.err_msg=msg;
						
					}else{
						Helper.BusEmitError(error);
					}
					this.submitting=false;
					
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		clearErrorMsg(name) {
      	    this.titleError='';
        },
	}
}
</script>

