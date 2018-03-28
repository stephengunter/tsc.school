<template>
    <div v-if="form">
        <div class="form-group">
            <label class="col-md-2 control-label">真實姓名</label>
            <div class="col-md-4">
                <input :readonly="readonly" type="text" name="user.profile.fullname" class="form-control" v-model="form.user.profile.fullname"  >
                <small class="text-danger" v-if="form.errors.has('user.profile.fullname')" v-text="form.errors.get('user.profile.fullname')"></small>
            </div>
            <label class="col-md-2 control-label">性別</label>
            <div class="col-md-4">
                <toggle :items="genderOptions"   :default_val="form.user.profile.gender" @selected="setGender"></toggle>
            
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">身分證號</label>
            <div class="col-md-4">
                <input :readonly="readonly"  type="text" name="user.profile.sid" class="form-control" v-model="form.user.profile.sid"  >
                <small class="text-danger" v-if="form.errors.has('user.profile.sid')" v-text="form.errors.get('user.profile.sid')"></small>
            </div>
            <label class="col-md-2 control-label">生日</label>
            <div class="col-md-4">
                <input v-if="readonly" class="form-control" :readonly="readonly"  type="text" :value="form.user.profile.dob"  >
                
                <datetime-picker v-else :date="form.user.profile.dob" :can_clear="true" @selected="setDOB"></datetime-picker>
                <small class="text-danger" v-if="form.errors.has('user.profile.dob')" v-text="form.errors.get('user.profile.dob')"></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Email</label>
            <div class="col-md-4">
                <input :readonly="readonly"  type="text" name="user.email" class="form-control" v-model="form.user.email" >
                <small class="text-danger" v-if="form.errors.has('user.email')" v-text="form.errors.get('user.email')"></small>
            </div>
            <label class="col-md-2 control-label">手機</label>
            <div class="col-md-4">
                <input :readonly="readonly"  type="text" name="user.phone" class="form-control" v-model="form.user.phone" >
                <small class="text-danger"  v-if="form.errors.has('user.phone')" v-text="form.errors.get('user.phone')"></small>
            </div>
        </div>
			
    </div>   
</template>


<script>
    export default {
        name: 'UserCreateInputs',
        props: {
            form: {
                type: Object,
                default: null
            },
            readonly:{
                type: Boolean,
                default: false
            }
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