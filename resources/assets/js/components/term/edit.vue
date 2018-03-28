<template>
    <div class="panel-body">
        <form v-if="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
			<div class="form-group">
				<label class="col-md-2 control-label">年度</label>
				<div class="col-md-4">
					<select v-model="form.term.year"  class="form-control">
                        <option v-for="(item,index) in yearOptions" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                    <small class="text-danger" v-if="form.errors.has('term.year')" v-text="form.errors.get('term.year')"></small>
                    
				</div>
                <label class="col-md-2 control-label">順序</label>
				<div class="col-md-4">
					<select v-model="form.term.order"  class="form-control">
                        <option v-for="(item,index) in orderOptions" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                    <small class="text-danger" v-if="form.errors.has('term.order')" v-text="form.errors.get('term.order')"></small>
                    
				</div>
                
            </div>
            <div class="form-group">
				<!-- <label class="col-md-2 control-label">名稱</label>
				<div class="col-md-4">
					<input type="text" name="term.name" v-model="form.term.name" class="form-control"  >
                    <small class="text-danger" v-if="form.errors.has('term.name')" v-text="form.errors.get('term.name')"></small>
				</div> -->
                <label class="col-md-2 control-label">狀態</label>
				<div class="col-md-4">
					<toggle :items="activeOptions"   :default_val="form.term.active" @selected="setActive"></toggle>
				</div>
                
            </div>
            <div class="form-group">
				<label class="col-md-2 control-label">報名起始日	</label>
				<div class="col-md-10">
					<datetime-picker :date="form.term.openDate" @selected="setOpenDate"></datetime-picker>
                    <small class="text-danger" v-if="form.errors.has('term.openDate')" v-text="form.errors.get('term.openDate')"></small>
				</div>
            </div>
            <div class="form-group">
				<label class="col-md-2 control-label">早鳥截止日	</label>
				<div class="col-md-10">
					<datetime-picker :date="form.term.birdDate" @selected="setBirdDate"></datetime-picker>
                    <small class="text-danger" v-if="form.errors.has('term.birdDate')" v-text="form.errors.get('term.birdDate')"></small>
				</div>
            </div>
            <div class="form-group">
				<label class="col-md-2 control-label">開課決定日	</label>
				<div class="col-md-10">
					<datetime-picker :date="form.term.closeDate" @selected="setCloseDate"></datetime-picker>
                    <small class="text-danger" v-if="form.errors.has('term.closeDate')" v-text="form.errors.get('term.closeDate')"></small>
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
						<i class="fa fa-save"></i>
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
    name:'EditTerm',
    props: {
        id: {
            type: Number,
            default: 0
        },
    },
	data(){
		return {

            yearOptions:[],
            orderOptions:[],

            activeOptions:Helper.activeOptions(),

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
		cancel(){
			this.$emit('cancel');
		},
		init(){
			this.fetchData();
		},
		fetchData(){
            let getData=null;
            if(this.id) getData=Term.edit(this.id);
            else getData=Term.create();

			getData.then(model => {
				
				this.form = new Form({
					term:{
						...model.term
					}
				});

                this.yearOptions=model.yearOptions.slice(0);
                this.orderOptions=model.orderOptions.slice(0);
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
        setActive(val) {
            this.form.term.active = val;
        },
        setOpenDate(val){
			this.form.term.openDate=val;
			this.clearErrorMsg('term.openDate');
        },
        setBirdDate(val){
			this.form.term.birdDate=val;
			this.clearErrorMsg('term.birdDate');
        },
        setCloseDate(val){
			this.form.term.closeDate=val;
			this.clearErrorMsg('term.closeDate');
        },
		onSubmit(){
			
            let save=null;
            if(this.id) save=Term.update(this.id,this.form);
            else  save=Term.store(this.form); 

			save.then(term => {
                    this.$emit('saved');
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

