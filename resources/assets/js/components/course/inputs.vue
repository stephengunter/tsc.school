<template>

<div>
    <div class="row">
        <div v-if="!isCreate" class="col-sm-3">
            <div class="text-center">
                <!-- <photo :id="photo_id"></photo>
                <h5>相片</h5>
                <button  @click.prevent="editPhoto" title="編輯相片" class="btn btn-info btn-xs">                                 
                    <span class="glyphicon glyphicon-pencil"></span>
                </button> 
                <button v-show="photo_id" @click.prevent="onBtnDeletePhotoClicked" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
                    <span class="glyphicon glyphicon-trash"></span>
                </button> -->
                
            </div>
        </div>  
        <div v-bind:class="[ isCreate ? 'col-sm-12' : 'col-sm-9']">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">                           
                        <label>名稱</label>
                        <input type="text" name="course.name" class="form-control" v-model="form.course.name">
                        <small class="text-danger" v-if="form.errors.has('course.name')" v-text="form.errors.get('course.name')"></small>
                    </div>
                </div>  
                <div class="col-sm-3">
                    <div class="form-group">                           
                        <label>程度</label>
                        <input type="text" name="course.level" class="form-control" v-model="form.course.level">
                    </div>
                                            
                </div> 
                <div class="col-sm-3">
                    <div class="form-group">                           
                        <label>開課中心</label>
                        <select   v-model="form.course.centerId"  name="course.centerId" class="form-control" >
                            <option v-for="(item,index) in centers" :key="index"  :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                                            
                </div> 
                <div class="col-sm-2">
                    <div class="form-group">
                        <label>學期別</label>
                        <select v-model="form.course.termId"  name="course.termId" class="form-control" >
                            <option v-for="(item,index) in terms" :key="index"  :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                    
                </div>    
            </div>  <!-- End Row   --> 
            
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">  
                        <label>流水號</label>
                        <input type="text" name="course.number" class="form-control" v-model="form.course.number"  >
                        <small class="text-danger" v-if="form.errors.has('course.number')" v-text="form.errors.get('course.number')"></small>
                    </div>
                </div> 
                <div class="col-sm-3">
                    <div class="form-group">  
                        <label>課程分類</label>
                        <select v-model="form.course.categoryId"  name="course.categoryId" class="form-control" >
                            <option v-for="(item,index) in categories" :key="index"  :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                </div>  
                <div class="col-sm-3">
                    <div  class="form-group">  
                        <label>教師</label>
                        <v-select :value.sync="form.teacherIds" multiple  :options="teachers" :onChange="onTeacherChanged" label="text">
                            <slot name="no-options">-----</slot>
                        </v-select>
                        <small class="text-danger" v-if="!form.teacherIds.length" >請選擇教師</small>
                    </div>
                                            
                </div> 
                <div class="col-sm-3">
                    <div  class="form-group">  
                        <label>教育志工</label>
                        <v-select :value.sync="form.volunteerIds" multiple  :options="volunteers" :onChange="onVolunteerChanged" label="text">
                            <slot name="no-options">-----</slot>
                        </v-select>
                       
                    </div>
                    
                </div>    
            </div>  <!-- End Row   --> 
                
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">  
                        <label>課程簡介</label>
                        <textarea rows="4" cols="50" class="form-control" name="course.description"  v-model="form.course.description">
                        </textarea>
                        <small class="text-danger" v-if="form.errors.has('course.description')" v-text="form.errors.get('course.description')"></small>
                    </div> 
                </div>    
            
            </div> <!-- End Row   --> 
            <div  class="row">
                <div class="col-sm-4">
                    <div class="form-group">  
                        <label>起始日期</label>
                        <div>
                            <datetime-picker :date="form.course.beginDate" @selected="setBeginDate"></datetime-picker>
                        </div>
                        <small class="text-danger" v-if="form.errors.has('course.beginDate')" v-text="form.errors.get('course.beginDate')"></small>
                    </div>
                </div> 
                <div class="col-sm-4">
                    <div class="form-group">  
                        <label>結束日期</label>
                        <div>
                            <datetime-picker :date="form.course.endDate" @selected="setEndDate"></datetime-picker>
                        </div>
                        <small class="text-danger" v-if="form.errors.has('course.endDate')" v-text="form.errors.get('course.endDate')"></small>
                    </div>
                </div> 
            </div>      <!-- End Row   -->  
            <div  class="row">
                <div class="col-sm-4">
                    <div  class="form-group">  
                        <label>週數</label>
                        <select  v-model="form.course.weeks"  name="course.weeks" class="form-control" style="width:120px">
                            <option v-for="(item,index) in weeksOptions" :key="index" :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                </div> 
                <div class="col-sm-3">
                    <div class="form-group">  
                        <label>時數</label>
                        <select  v-model="form.course.hours"  name="course.hours" class="form-control"  style="width:120px">
                            <option v-for="(item,index) in hoursOptions" :key="index" :value="item.value" v-text="item.text"></option>
                        </select>
                    </div>
                </div> 
                
            </div>      <!-- End Row   --> 
            <div  class="row">
                <div class="col-sm-12">
                    <div  class="form-group">  
                        <label>備註</label>
                        <input type="text" name="course.ps" class="form-control" v-model="form.course.ps"  >
                    </div>
                </div> 
                
                
            </div>      <!-- End Row   --> 
            
               
        </div>
    </div>
    
</div>  

</template>


<script>
    export default {
        name: 'CourseInputs',
        props: {
            form: {
                type: Object,
                default: null
            },
            centers:{
                type: Array,
                default: null
            },
            terms:{
                type: Array,
                default: null
            },
            categories:{
                type: Array,
                default: null
            },
            teachers:{
                type: Array,
                default: null
            },
            volunteers:{
                type: Array,
                default: null
            },
        },
        data() {
            return {
                
                weeksOptions:Helper.numberOptions(1, 30),
                hoursOptions:Helper.numberOptions(1, 99)
            }
        },
        computed:{
            isCreate(){
                if(!this.form.course.id) return true;
                return parseInt(this.form.course.id) < 1;
            }
            
        },
        watch:{
           
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                
            },
            onTeacherChanged(val){
                if(val.length) this.clearErrorMsg('teacherIds');
            },
            onVolunteerChanged(val){
               
            },
            onCenterSelected(center){
               this.form.Course.centerId=center.value;
            },
            setActive(val){
                this.form.Course.active=val;
            },
            onCanceled(){
                this.$emit('canceled')
            },
            setBeginDate(val){
               this.form.course.beginDate=val;
            },
            setEndDate(val){
               this.form.course.endDate=val;
               this.clearErrorMsg('course.endDate');
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
        }
    }
</script>