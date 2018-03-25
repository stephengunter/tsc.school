<template>
<div v-if="form.teacher.group">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>名稱</label>
                <input type="text" name="teacher.name" class="form-control" v-model="form.teacher.name"  >
                <small class="text-danger" v-if="form.errors.has('teacher.name')" v-text="form.errors.get('teacher.name')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>所屬中心</label>
                <div>
                    <drop-down :items="centers" :selected="form.teacher.centerId"
                        @selected="onCenterSelected">
                    </drop-down>
                </div>        
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>狀態</label>
                <div>
                    <toggle :items="activeOptions"   :default_val="form.teacher.active" @selected="setActive"></toggle>
                </div>
                
            </div>
        </div>
    </div> <!--  row   -->
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">                           
                <label>簡介</label>
                <textarea rows="5"  class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
       
    </div> <!--  row   -->
</div>
<div v-else>
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
                <label>專長</label>
                <input type="text" name="teacher.specialty" class="form-control" v-model="form.teacher.specialty"  >
                <small class="text-danger" v-if="form.errors.has('teacher.specialty')" v-text="form.errors.get('teacher.specialty')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>最高學歷</label>
                <input type="text" name="teacher.education" class="form-control" v-model="form.teacher.education"  >
                <small class="text-danger" v-if="form.errors.has('teacher.education')" v-text="form.errors.get('teacher.education')"></small>
            </div>
        </div>
    </div> <!--  row   -->
    <div v-if="false" class="row">
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>現職</label>
                <input type="text" name="teacher.job" class="form-control" v-model="form.teacher.job"  >
                <small class="text-danger" v-if="form.errors.has('teacher.job')" v-text="form.errors.get('teacher.job')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>職稱</label>
                <input type="text" name="teacher.jobtitle" class="form-control" v-model="form.teacher.jobtitle"  >
                <small class="text-danger" v-if="form.errors.has('teacher.jobtitle')" v-text="form.errors.get('teacher.jobtitle')"></small>
            </div>
        </div>
        <div class="col-sm-4">
            
        </div>
    </div> <!--  row   -->
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>經歷</label>
                <textarea rows="6" cols="50" class="form-control" name="teacher.experiences"  v-model="form.teacher.experiences">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.experiences')" v-text="form.errors.get('teacher.experiences')"></small>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="form-group">
                <label>個人簡介</label>
                <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
        <div class="col-sm-4">
           
        </div>

    </div>  <!-- end row-->
    <div v-if="!group" class="row">
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>鐘點費</label>
					<input type="text" name="teacher.wage" class="form-control" v-model="form.teacher.wage"  >
					<small class="text-danger" v-if="form.errors.has('teacher.wage')" v-text="form.errors.get('teacher.wage')"></small>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">                           
					<label>銀行帳號</label>
					<input type="text" name="teacher.account" class="form-control" v-model="form.teacher.account"  >
					<small class="text-danger" v-if="form.errors.has('teacher.account')" v-text="form.errors.get('teacher.account')"></small>
				</div>
			</div>
			<div class="col-sm-4">
				
			</div>
    	</div> 
</div>  

</template>


<script>
    export default {
        name: 'TeacherInputs',
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
            }
        },
        data() {
            return {
                activeOptions:Helper.activeOptions(),
               
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
            onCenterSelected(center){
               
               this.form.teacher.centerId=center.value;
            },
            onCentersChanged(values){
                this.form.centerIds=values.slice(0);
                if(values.length)  this.form.errors.clear('centerIds');
            },
            setActive(val){
                this.form.teacher.active=val;
            },
            onCanceled(){
                this.$emit('canceled')
            },
            clearDOB(){
                    this.dob.time=''
            },
        }
    }
</script>