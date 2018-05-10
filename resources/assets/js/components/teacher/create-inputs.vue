<template>
<div v-if="form">
    <div v-if="group">
        <div v-if="canEditCenters" class="form-group">
            <label class="col-md-2 control-label">所屬中心</label>
            <div class="col-md-10">
                <div>
                    <select  v-model="form.teacher.centerId"  name="teacher.centerId" class="form-control" style="width:200px">
                       <option v-for="(item,index) in centers" :key="index" :value="item.value" v-text="item.text"></option>
                   </select>
                </div> 
            </div>
            
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">名稱</label>
            <div class="col-md-10">
                <input type="text" name="teacher.name" class="form-control" v-model="form.teacher.name"  >
                <small class="text-danger" v-if="form.errors.has('teacher.name')" v-text="form.errors.get('teacher.name')"></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">簡介</label>
            <div class="col-md-10">
                <textarea rows="5"  class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
    </div>
    <div v-else>
        <user-create-inputs :form="form">

        </user-create-inputs>

        <div v-if="canEditCenters" class="form-group">
            <label class="col-md-2 control-label">所屬中心</label>
            <div class="col-md-10">
                <check-box-list :options="centers" :default_values="form.centerIds" 
					@select-changed="onCentersChanged">
				</check-box-list>
                <small class="text-danger" v-if="form.errors.has('centerIds')" v-text="form.errors.get('centerIds')"></small>
            </div>
            
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">現職</label>
            <div class="col-md-4">
                <input type="text" name="teacher.jobtitle" class="form-control" v-model="form.teacher.jobtitle"  >
                <small class="text-danger" v-if="form.errors.has('teacher.jobtitle')" v-text="form.errors.get('teacher.jobtitle')"></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">專長</label>
            <div class="col-md-4">
                <input type="text" name="teacher.specialty" class="form-control" v-model="form.teacher.specialty"  >
                <small class="text-danger" v-if="form.errors.has('teacher.specialty')" v-text="form.errors.get('teacher.specialty')"></small>
            </div>
            <label class="col-md-2 control-label">最高學歷</label>
            <div class="col-md-4">
                <input type="text" name="teacher.education" class="form-control" v-model="form.teacher.education"  >
                <small class="text-danger" v-if="form.errors.has('teacher.education')" v-text="form.errors.get('teacher.education')"></small>
            </div>
        </div>
        
        <div  class="form-group">
            <label class="col-md-2 control-label">經歷</label>
            <div class="col-md-4">
                <textarea rows="6" cols="50" class="form-control" name="teacher.experiences"  v-model="form.teacher.experiences">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.experiences')" v-text="form.errors.get('teacher.experiences')"></small>
            </div>
            <label v-if="false" class="col-md-2 control-label">個人簡介</label>
            <div v-if="false" class="col-md-4">
                <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">薪酬標準</label>
            <div class="col-md-4">
                <drop-down :items="wages" :selected="form.teacher.wageId"
                    @selected="onWageSelected">
                </drop-down>
			</div>
            <label v-if="isSpecialPay" class="col-md-2 control-label">特殊講師鐘點費</label>
            <div v-if="isSpecialPay" class="col-md-4">
                <input type="text" name="teacher.pay" class="form-control" v-model="form.teacher.pay"  >
                <small class="text-danger" v-if="form.errors.has('teacher.pay')" v-text="form.errors.get('teacher.pay')"></small>
			</div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">市話</label>
            <div class="col-md-4">
                <input type="text"  name="contactInfo.tel" v-model="form.contactInfo.tel" class="form-control" >
                     
            </div>
            
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">通訊地址</label>
            <div class="col-md-10 form-inline">
                <select v-model="form.contactInfo.address.cityId" @change="onCityChanged" style="width:auto;" class="form-control selectWidth">
                    <option v-for="(city,index) in cities" :key="index" :value="city.id" v-text="city.name" >
                    </option>
                </select>
                <select v-model="form.contactInfo.address.districtId" style="width:auto;" class="form-control selectWidth">
                    <option v-for="(d,index) in districts" :key="index" :value="d.id" v-text="d.name">
                    </option>
                </select> 
                <input type="text"  name="contactInfo.address.street" v-model="form.contactInfo.address.street" class="form-control" style="width:480px" >
                <small class="text-danger" v-if="form.errors.has('contactInfo.address.street')" v-text="form.errors.get('contactInfo.address.street')"></small>
                        
            </div>
            
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">第二地址</label>
            <div class="col-md-10 form-inline">
                <select v-model="ubeAddress.cityId" @change="onUbeCityChanged" style="width:auto;" class="form-control selectWidth">
                    <option v-for="(city,index) in cities" :key="index" :value="city.id" v-text="city.name" >
                    </option>
                </select>
                <select v-model="ubeAddress.districtId" style="width:auto;" class="form-control selectWidth">
                    <option v-for="(d,index) in ubeDistricts" :key="index" :value="d.id" v-text="d.name">
                    </option>
                </select> 
                <input type="text"  name="ubeAddress.street" v-model="ubeAddress.street" class="form-control" style="width:480px" >
                     
            </div>
            
        </div>
        
        <div  class="form-group">
            <label class="col-md-2 control-label">備註</label>
            <div class="col-md-10">
                
                <input type="text" name="teacher.ps" class="form-control" v-model="form.teacher.ps"  >
            </div>
            
        </div>
        
    </div>
</div>    
</template>

<script>
import UserInputs from '../user/create-inputs';
export default {
    name: 'TeacherCreateInputs',
    components: {
        'user-create-inputs':UserInputs,
    },
    props: {
        group:{
            type: Boolean,
            default: false
        },
        form: {
            type: Object,
            default: null
        },
        centers:{
            type: Array,
            default: null
        },
        wages:{
            type: Array,
            default: null
        },
        cities:{
            type: Array,
            default: null
        }
    },
    data(){
		return {
            isSpecialPay:false,
            districts:[],
            ubeDistricts:[],

            ubeAddress:{
                cityId:0,
                districtId:0,
                street:''
            }
		}
	},
    computed:{
		canEditCenters(){
            if(!this.centers) return false;
            return this.centers.length > 1
        },
        
    },
    beforeMount() {
        this.init()
    },
    methods:{
        init() {
            if(!this.group){
               
                let wage=this.wages.find(item=>{
                    return item.value==this.form.teacher.wageId;
                });

                this.isSpecialPay=Wage.isSpecial(wage);

                this.onCityChanged();

                this.ubeAddress.cityId=this.form.contactInfo.address.cityId;               
                this.onUbeCityChanged();
            
            }

            
           

        },
        onWageSelected(item){
            this.form.teacher.wageId=item.value;
            
            this.isSpecialPay= Wage.isSpecial(item);
            
        },
        onCityChanged(){
            let getDistrictId=this.loadDistricts();	
            getDistrictId.then(districtId => {
                this.form.contactInfo.address.districtId=districtId;
            })
        },
        onUbeCityChanged(){
            let getDistrictId=this.loadUbeDistricts();	
            getDistrictId.then(districtId => {
                this.ubeAddress.districtId=districtId;
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
        loadUbeDistricts(){
            return new Promise((resolve, reject) => {
                
                let cityId=this.ubeAddress.cityId;
                let city= this.cities.find((item)=>{
                    return item.id == cityId;
                });
            
                this.ubeDistricts=city.districts.slice(0);
                resolve(this.ubeDistricts[0].id);
                

            })
        },
        onCentersChanged(values){
            this.form.centerIds=values.slice(0);
            if(values.length)  this.form.errors.clear('centerIds');
        },
        onCanceled(){
            this.$emit('canceled')
        },
        clearDOB(){
            this.dob.time=''
        },
        getUbeAddress(){
            if(!this.ubeAddress.street) return '';
            let city= this.cities.find((item)=>{
                    return item.id == this.ubeAddress.cityId;
            });
            let district= this.ubeDistricts.find((item)=>{
                return item.id == this.ubeAddress.districtId;
            });
            return city.name + district.name + this.ubeAddress.street;
        }
    }
}
</script>

