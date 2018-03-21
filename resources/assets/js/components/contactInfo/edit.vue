<template>
    <div v-if="form" class="panel-body">
        <form class="form-horizontal" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
            <div class="row">
                <div class="form-group">
                    <label  class="col-md-2 control-label" >市話</label>
                    <div class="col-md-8">
                    <input type="text" name="contactInfo.tel" v-model="form.contactInfo.tel" class="form-control"  >
                    <small class="text-danger" v-if="form.errors.has('contactInfo.tel')" v-text="form.errors.get('contactInfo.tel')"></small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" >傳真</label>
                    <div class="col-md-8">
                        <input type="text" name="contactInfo.fax" v-model="form.contactInfo.fax" class="form-control"  >
                        <small class="text-danger" v-if="form.errors.has('contactInfo.fax')" v-text="form.errors.get('contactInfo.fax')"></small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" >通訊地址</label>
                    <div class="col-md-8  form-inline">
                        <select v-if="can_options"  v-model="form.contactInfo.address.cityId" @change="onCityChanged" style="width:auto;" class="form-control selectWidth">
                            <option v-for="(city,index) in cities" :key="index" :value="city.id" v-text="city.name" >
                            </option>
                        
                        </select>
                        <select  v-if="can_options" v-model="form.contactInfo.address.districtId" style="width:auto;" class="form-control selectWidth">
                            <option v-for="(d,index) in districts" :key="index" :value="d.id" v-text="d.name">
                            </option>
                        </select> 
                        <input type="text"  name="contactInfo.address.street" v-model="form.contactInfo.address.street" class="form-control" style="width:480px" >
                        <small class="text-danger" v-if="form.errors.has('contactInfo.address.street')" v-text="form.errors.get('contactInfo.address.street')"></small>
                        
            
                    </div>
                </div> 
                <div  class="form-group">
                    <div class="col-md-2">
                        
                    </div> 
                    <div v-if="submitting"  class="col-md-8">
                        <button class="btn btn-default">
                            <i class="fa fa-spinner fa-spin"></i> 
                            處理中
                        </button> 
                    </div>
                    <div v-else  class="col-md-8">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i>
                            確認存檔
                        </button> 
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-default" @click.prevent="cancel">
                            取消
                        </button>  
                    </div>  
                    
                </div>
                
               
            </div><!--  Row  -->
        </form>
    </div>
   
</template>
<script>
   
    export default {
        name: 'EditContactInfo',
        props: {
            id: {
              type: Number,
              default: 0
            },
            can_edit:{
               type: Boolean,
               default: true
            },
            user_id:{
              type: String,
              default: ''
            },
            center_id:{
              type: Number,
              default: 0
            },
            can_options:{
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                
                form:null,
                cities:[],
                districts:[],

                submitting:false,

            }
        },
        
        beforeMount() {
            this.init();
        },
        methods: {
            init(){
                this.fetchData();
            },
            fetchData() {
                
                let id=this.id
                let getData = null
                if(id){
                   getData=ContactInfo.edit(id);
                }
                else {
                   getData=ContactInfo.create();
                }
             
                getData.then(model => {
                    this.form = new Form({
                        contactInfo:{
                            ...model.contactInfo
                        }
                    });

                    

                    if(this.can_options){
                        this.cities=model.cityOptions.slice(0);
                        if(id){
                            this.loadDistricts();
                        }else{
                            this.onCityChanged();
                        }
                        
                    }
                })
                .catch(error=> {
                    Helper.BusEmitError(error);    
                })
                
            },
            onCityChanged(){
                let getDistrictId=this.loadDistricts();	
			    getDistrictId.then(districtId => {
					this.form.contactInfo.address.districtId=districtId;
				})
            },
            loadDistricts(){
                return new Promise((resolve, reject) => {
					
					let cityId=this.form.contactInfo.address.cityId;
                    let city= this.cities.find((item)=>{
                        return item.id == cityId;
                    });
               
                    this.districts=city.districts.slice(0);
					resolve(this.districts[0].id);
					

				})
                

            },
            onSubmit() {
                this.submitting=true;

                this.form.contactInfo.userId=this.user_id;
                this.form.contactInfo.centerId=this.center_id;

                
                let save=null;
                if (this.id) {
                    save=ContactInfo.update(this.id,this.form)
                }else{
                    save=ContactInfo.store(this.form)
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
           
            cancel(){
                this.$emit('canceled')
            },




        },

    }
</script>