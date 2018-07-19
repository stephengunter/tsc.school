<template>
<div v-if="form">
    
    <div>
        <user-create-inputs :form="form">

        </user-create-inputs>
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
        
        <div v-if="false" class="form-group">
            <label class="col-md-2 control-label">可服務時間</label>
            <div class="col-md-10">
                
                <check-box-list :options="weekdays" :default_values="form.weekdayIds" 
					@select-changed="onWeekdaysChanged">
				</check-box-list>
            </div>
            
        </div>
        <div v-if="false" class="form-group">
            <label class="col-md-2 control-label">可服務時段</label>
            <div class="col-md-2">
                <select v-model="form.volunteer.time" class="form-control">
                    <option v-for="(item,index) in time_options" :key="index" :value="item.value" v-text="item.text" >
                    </option>
                </select>
                
            </div>
            
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">備註</label>
            <div class="col-md-10">
                <input type="text" name="volunteer.ps" class="form-control" v-model="form.volunteer.ps"  >
                
            </div>
            
        </div>

        <div v-if="canEditCenters" class="form-group">
            <label class="col-md-2 control-label">所屬中心</label>
            <div class="col-md-10">
                <check-box-list :options="centers" :default_values="form.centerIds" 
					@select-changed="onCentersChanged">
				</check-box-list>
                <small class="text-danger" v-if="form.errors.has('centerIds')" v-text="form.errors.get('centerIds')"></small>
            </div>
            
        </div>
    </div>
</div>    
</template>

<script>
import UserInputs from '../user/create-inputs';
export default {
    name: 'VolunteerCreateInputs',
    components: {
        'user-create-inputs':UserInputs,
    },
    props: {
        form: {
            type: Object,
            default: null
        },
        centers:{
            type: Array,
            default: null
        },
        weekdays:{
            type: Array,
            default: null
        },
        cities:{
            type: Array,
            default: null
        },
        time_options:{
            type: Array,
            default: null
        },
    },
    data(){
		return {
           districts:[],
          
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
            this.onCityChanged();     
        },
        onCentersChanged(values){
            this.form.centerIds=values.slice(0);
            if(values.length)  this.form.errors.clear('centerIds');
        },
        onWeekdaysChanged(values){
            this.form.weekdayIds=values.slice(0);
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
        onCanceled(){
            this.$emit('canceled')
        }
    }
}
</script>

