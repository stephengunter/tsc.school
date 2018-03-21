<template>
   <div>
		<div class="row">
			<div class="col-sm-3">
				<h3>{{ title  }}</h3>
			</div>
			<div class="col-sm-3">
				
			</div>
			<div class="col-sm-3" style="margin-top: 20px;">
				
			</div>
			<div class="col-sm-3" style="margin-top: 20px;">

				<a @click.prevent="cancel" href="#" class="btn btn-default pull-right">
					<i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i>
					返回
				</a>
			</div>
		</div>
        <hr/>
        <form v-if="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)" class="form-horizontal">
			<div class="form-group">
				<label class="col-md-2 control-label">名稱</label>
				<div class="col-md-4">
					<input name="center.name" v-model="form.center.name" class="form-control" />
					<small class="text-danger" v-if="form.errors.has('center.name')" v-text="form.errors.get('center.name')"></small>
				
				</div>
                <label class="col-md-2 control-label">代碼</label>
				<div class="col-md-4">
					<input name="center.code" v-model="form.center.code" class="form-control" />
					<small class="text-danger" v-if="form.errors.has('center.code')" v-text="form.errors.get('center.code')"></small>
				
				</div>
            </div>
            <div class="form-group">
                    <label class="col-md-2 control-label">所在地</label>
                    <div class="col-md-4">
                        <div>
                            <toggle :items="overseaOptions"   :default_val="form.center.oversea" @selected="setOversea"></toggle>
                        </div>
                    </div>
                    <label v-show="!oversea" class="col-md-2 control-label">區域</label>
                    <div v-show="!oversea" class="col-md-4">
                        <select v-model="form.center.areaId" class="form-control">
                            <option v-for="(item,index) in areaOptions" :key="index" :value="item.value" v-text="item.text" >
                            </option>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">課程洽詢電話</label>
                <div class="col-md-10">
                    <input name="center.courseTel" v-model="form.center.courseTel" class="form-control" />
                    <small class="text-danger" v-if="form.errors.has('center.courseTel')" v-text="form.errors.get('center.courseTel')"></small>
            
                </div>
            </div>
			<div class="form-group">
				<label class="col-md-2 control-label">市話</label>
				<div class="col-md-4">
					<input name="center.contactInfo.tel" v-model="form.center.contactInfo.tel" class="form-control" />
					<small class="text-danger" v-if="form.errors.has('center.contactInfo.tel')" v-text="form.errors.get('center.contactInfo.tel')"></small>
				
				</div>
                <label class="col-md-2 control-label">傳真</label>
				<div class="col-md-4">
					<input name="center.contactInfo.fax" v-model="form.center.contactInfo.fax" class="form-control" />
					<small class="text-danger" v-if="form.errors.has('center.contactInfo.fax')" v-text="form.errors.get('center.contactInfo.fax')"></small>
				
				</div>
            </div>
            <div class="form-group">
				<label class="col-md-2 control-label">地址</label>
				<div class="col-md-10 form-inline">
					<select v-model="form.center.contactInfo.address.cityId" @change="onCityChanged" style="width:auto;" class="form-control selectWidth">
                        <option v-for="(city,index) in cities" :key="index" :value="city.id" v-text="city.name" >
                        </option>
                    
                    </select>
                    <select  v-model="form.center.contactInfo.address.districtId" style="width:auto;" class="form-control selectWidth">
                        <option v-for="(d,index) in districts" :key="index" :value="d.id" v-text="d.name">
                        </option>
                    </select> 
                    <input type="text"  name="center.contactInfo.address.street" v-model="form.center.contactInfo.address.street" class="form-control" style="width:480px" >
                    <small class="text-danger" v-if="form.errors.has('center.contactInfo.address.street')" v-text="form.errors.get('center.contactInfo.address.street')"></small>
                    
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
	name:'CenterCreateView',
	data(){
		return {
			
            title:'新增開課中心',
            oversea:false,
            overseaOptions:Center.overseaOptions(),

            areaOptions:[],

            cities:[],
            districts:[],
			//categoryOptions:[],

			//topOptions:Post.topOptions(),

			//reviewedOptions:Post.reviewedOptions(),

			

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
			let getData=Center.create();

			getData.then(model => {
				
				this.form = new Form({
					center:{
						...model.center
					}
				});

                this.areaOptions=model.areaOptions.slice(0);
                
                this.cities=model.cityOptions.slice(0);

                this.onCityChanged();
				
			})
			.catch(error=> {
				Helper.BusEmitError(error); 
			})
        },
        onCityChanged(){
            let getDistrictId=this.loadDistricts();	
            getDistrictId.then(districtId => {
                this.form.center.contactInfo.address.districtId=districtId;
            })
        },
        loadDistricts(){
            return new Promise((resolve, reject) => {
                
                let cityId=this.form.center.contactInfo.address.cityId;
                let city= this.cities.find((item)=>{
                    return item.id == cityId;
                });
            
                this.districts=city.districts.slice(0);
                resolve(this.districts[0].id);
                

            })
            

        },
        setOversea(val){
            this.oversea=Helper.isTrue(val);
            this.form.center.oversea=this.oversea;
        },
		onCategorySelected(category){
			this.form.center.categoryId=category.value;
		},
		// setReviewed(val) {
        //     this.form.center.reviewed = val;
		// },
		// setTop(val) {
        //     this.form.center.top = val;
        // },
		
		onSubmit(){
            
            if(this.oversea){
                this.form.center.areaId=0;
            }
			
			let save=Center.store(this.form); 

			save.then(() => {
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

