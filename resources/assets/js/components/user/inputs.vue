<template>

<div v-if="form">
    <div class="row">
        <div class="col-sm-3">
            <div class="text-center">
                <!-- <photo :id="photo_id"></photo>
                <h5>個人相片</h5>
                <button @click.prevent="editPhoto(1)" title="編輯相片" class="btn btn-info btn-xs">                                 
                    <span class="glyphicon glyphicon-pencil"></span>
                </button> 
                <button v-show="photo_id" @click.prevent="editPhoto(0)" type="button" class="btn btn-danger btn-xs"  data-toggle="tooltip" title="刪除相片">
                    <span class="glyphicon glyphicon-trash"></span>
                </button> -->
                
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">                           
                <label>真實姓名</label>
                <input type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname"  >
                <small class="text-danger" v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')"></small>
            </div>
            <div class="form-group">
                <label>身分證號</label>
                <input type="text" name="user.profile.sid" class="form-control" v-model="form.user.profile.sid"  >
                <small class="text-danger" v-if="form.errors.has('user.profile.sid')" v-text="form.errors.get('user.profile.sid')"></small>
            </div>
            <div class="form-group">
                
                <label>Email</label>
                <input type="text" name="user.email" class="form-control" v-model="form.user.email" >
                <small class="text-danger" v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')"></small>
            </div>
            
            
        </div>
            <div class="col-sm-3">
            
            <div class="form-group">
                <label>性別</label>
                <div>
                    <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected="setGender"></toggle>
                </div>
            </div>
            <div class="form-group">
                <label>生日</label>
                <datetime-picker :date="form.user.profile.dob" :can_clear="true" @selected="setDOB"></datetime-picker>
                <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
            </div>
            <div class="form-group">
                <label>手機</label>
                <input type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
                <small class="text-danger"  v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')"></small>
            </div>
            
        </div>
        <div class="col-sm-3">
            
            
            
        </div>
    </div>
    
                    
                
       
    
</div>  
</template>


<script>
    export default {
        name: 'UserInputs',
        props: {
            form: {
                type: Object,
                default: null
            },
        },
        data() {
            return {
                genderOptions:Helper.genderOptions()
                
            }
        },
        computed:{
            
            
        },
        watch:{
           
        },
        beforeMount() {
            this.init()
        },
        methods: {
            init() {
                
            },
            setGender(val){
                this.form.user.profile.gender=val;
            },
            setDOB(val){
                this.form.user.profile.dob=val;
                this.clearErrorMsg('user.profile.dob');
            },
            onCanceled(){
                this.$emit('canceled')
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
        }
    }
</script>