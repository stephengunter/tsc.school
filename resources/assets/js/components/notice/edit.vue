<template>

<div v-if="form">    
    <form @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
		<div class="form-group">
			<label class="col-md-2 control-label">公告中心</label>
			<div class="col-md-10">
				<drop-down :items="centerOptions" :selected="form.notice.centerId"
					@selected="onCenterSelected">
				</drop-down>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">標題</label>
			<div class="col-md-10">
				<input name="post.title" v-model="form.notice.title" class="form-control" />
				<small class="text-danger" v-if="form.errors.has('notice.title')" v-text="form.errors.get('notice.title')"></small>
			
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-md-2 control-label">內容</label>
			<div class="col-md-10">
				<html-editor ref="contentEditor" :model="form.notice.content" :toolbar="textEditor.toolbar"
					:height="textEditor.height" @html-value="setContent">  
				</html-editor>  

				<small class="text-danger" v-if="form.errors.has('notice.content')" v-text="form.errors.get('notice.content')"></small>
			</div>
		</div>
		<div class="form-group">
			<label class="col-md-2 control-label">日期</label>
			<div class="col-md-10">
				<div class="form-group row">
					<div class="col-md-3">
						<datetime-picker :date="form.notice.date" @selected="setDate"></datetime-picker>
					</div>
					
				</div>
			</div>
		</div>
		<div v-if="canReview" class="form-group">
			<label class="col-md-2 control-label">置頂</label>
			<div class="col-md-10">
				<input type="hidden" v-model="form.notice.top"  >
				<toggle :items="topOptions"   :default_val="form.notice.top" @selected="setTop"></toggle>
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-md-2 control-label">狀態</label>
			<div class="col-md-10">
				<input type="hidden" v-model="form.notice.active"  >
				<toggle :items="activeOptions"   :default_val="form.notice.active" @selected="setActive"></toggle>
			</div>
		</div>

		<div v-if="canReview" v-show="!isCreate" class="form-group">
			<label class="col-md-2 control-label">審核</label>
			<div class="col-md-10">
				<input type="hidden" v-model="form.notice.reviewed"  >
				<toggle :items="reviewedOptions"   :default_val="form.notice.reviewed" @selected="setReviewed"></toggle>
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
    name:'EditNotice',
    props: {
        id: {
            type: Number,
            default: 0
        },
	},
	data(){
		return {

            form:null,

			canReview:false,

			topOptions:Helper.boolOptions(),
			activeOptions:Notice.activeOptions(),
			reviewedOptions:Notice.reviewedOptions(),

            textEditor:{
				height:360,
				toolbar:[]
			},
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
			this.fetchData();
		},
		cancel(){
			this.$emit('cancel');
		},
		fetchData(){
			let getData=null;
			if(this.id) getData=Notice.edit(this.id);
            else getData=Notice.create(this.id);

			getData.then(model => {
				
				this.form = new Form({
					notice:{
						...model.notice
					},
					
                });

				this.centerOptions=model.centerOptions.slice(0);
				

				this.canReview=model.canReview;

				this.$emit('loaded',model.notice);
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
		onCenterSelected(item){
			this.form.notice.centerId=item.value;
		},
		setContent(val){
			this.form.notice.content=val;
			this.onSubmit();
		},
		setDate(val){
			this.form.notice.date=val;
		},
		setActive(val) {
         	this.form.notice.active = val;
		},
		setReviewed(val) {
         	this.form.notice.reviewed = val;
		},
		setTop(val) {
			this.form.notice.top = val;
		},
		onSubmit(){
			this.submitting=true;

			let contentValue=this.$refs.contentEditor.getValue();
			this.form.notice.content=contentValue;

			let save=null;
			if(this.id) {
				save=  Notice.update(this.id,this.form)
			}
			else{
				save=Notice.store(this.form);
			} 

			save.then(() => {
					this.submitting=false;
                    this.$emit('saved');
					Helper.BusEmitOK('資料已存檔');
					
				})
				.catch(error => {
					this.submitting=false;
					this.$emit('saved');
					Helper.BusEmitError(error,'存檔失敗');
				})
			
		},
		clearErrorMsg(name) {
      	    this.form.errors.clear(name);
        },
	}
}
</script>

