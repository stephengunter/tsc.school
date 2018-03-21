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
        <div v-if="false"  class="form-group">
            <label class="col-md-2 control-label">現職</label>
            <div class="col-md-4">
                <input type="text" name="teacher.jobtitle" class="form-control" v-model="form.teacher.jobtitle"  >
                <small class="text-danger" v-if="form.errors.has('teacher.jobtitle')" v-text="form.errors.get('teacher.jobtitle')"></small>
            </div>
            <label class="col-md-2 control-label">職稱</label>
            <div class="col-md-4">
                <input type="text" name="teacher.jobtitle" class="form-control" v-model="form.teacher.jobtitle"  >
                <small class="text-danger" v-if="form.errors.has('teacher.jobtitle')" v-text="form.errors.get('teacher.jobtitle')"></small>
            </div>
        </div>
        <div  class="form-group">
            <label class="col-md-2 control-label">經歷</label>
            <div class="col-md-4">
                <textarea rows="6" cols="50" class="form-control" name="teacher.experiences"  v-model="form.teacher.experiences">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.experiences')" v-text="form.errors.get('teacher.experiences')"></small>
            </div>
            <label class="col-md-2 control-label">個人簡介</label>
            <div class="col-md-4">
                <textarea rows="6" cols="50" class="form-control" name="teacher.description"  v-model="form.teacher.description">
                </textarea>
                
                <small class="text-danger" v-if="form.errors.has('teacher.description')" v-text="form.errors.get('teacher.description')"></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">鐘點費</label>
            <div class="col-md-4">
                <input type="text" name="teacher.wage" class="form-control" v-model="form.teacher.wage"  >
				<small class="text-danger" v-if="form.errors.has('teacher.wage')" v-text="form.errors.get('teacher.wage')"></small>
			</div>
            <label class="col-md-2 control-label">銀行帳號</label>
            <div class="col-md-4">
                <input type="text" name="teacher.account" class="form-control" v-model="form.teacher.account"  >
				<small class="text-danger" v-if="form.errors.has('teacher.account')" v-text="form.errors.get('teacher.account')"></small>
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
        }
    },
    data(){
		return {
           
		}
	},
    computed:{
		canEditCenters(){
            if(!this.centers) return false;
            return this.centers.length > 1
        },
        
    },
    methods:{
        init() {
                
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
    }
}
</script>

