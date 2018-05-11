<template>
    <div>
        <div v-if="canEditCenters" class="row">
            <div class="col-sm-12">
                <div class="form-group">                           
                    <label>所屬中心</label>
                    <check-box-list :options="centers" :default_values="form.centerIds" 
                        @select-changed="onCentersChanged">
                    </check-box-list>
                    <small class="text-danger" v-if="form.errors.has('centerIds')" v-text="form.errors.get('centerIds')"></small>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">                           
                    <label>可服務時段</label>
                    <select v-model="form.volunteer.time" class="form-control">
                        <option v-for="(item,index) in time_options" :key="index" :value="item.value" v-text="item.text" >
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="form-group">                           
                    <label>可服務時間</label>
                    <check-box-list :options="weekdays" :default_values="form.weekdayIds" 
                        @select-changed="onWeekdaysChanged">
                    </check-box-list>
                </div>
            </div>
            <div v-if="false" class="col-sm-4">
                <div class="form-group">                           
                    <label>加入日期</label>
                    <datetime-picker :date="form.volunteer.joinDate" :can_clear="true" @selected="setJoinDate"></datetime-picker>
                </div>
            </div>
            
        </div> <!--  row   -->
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">                           
                    <label>備註</label>
                    <input type="text" name="volunteer.ps" class="form-control" v-model="form.volunteer.ps"  >
                   
                </div>
            </div>
            
        </div> <!--  row   -->
    </div>
</template>


<script>
    export default {
        name: 'VolunteerInputs',
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
            time_options:{
                type: Array,
                default: null
            },
        },
        data() {
            return {
              
               
            }
        },
        computed:{
            canEditCenters(){
                if(!this.centers) return false;
                return this.centers.length > 1
            },
            
        },
        watch:{
           
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                
            },
            onCanceled(){
                this.$emit('canceled')
            },
            onCentersChanged(values){
                this.form.centerIds=values.slice(0);
                if(values.length)  this.form.errors.clear('centerIds');
            },
            onWeekdaysChanged(values){
                this.form.weekdayIds=values.slice(0);
            },
            setJoinDate(val){
                this.form.volunteer.joinDate=val;
            },
        }
    }
</script>