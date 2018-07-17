<template>
    <div v-if="form">
        <form  class="form">
            
            <div class="form-group">
                <label class="col-md-1 control-label">報名日期</label>
                <div class="col-md-3">
                    <datetime-picker :date="form.signup.date" :can_clear="false" @selected="setDate"></datetime-picker>
            
                </div>

                
            </div>
               
			
        </form>
        <signup-details text="報名課程" :model="form.signup"
            @add-detail="onAddDetail" @remove-detail="onRemoveDetail">

        </signup-details>

        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title">
                <h4>學員資料</h4>
                </span> 
            </div>  
            <div class="panel-body">
                <user-view v-if="existUser" :model="this.form.user" :role="userSettings.role" ref="userView"
                   :can_edit="userSettings.can_edit" :can_back="userSettings.can_back" 
                   @back="resetUser" @saved="onUserUpdated"> 
                </user-view>
                <user-create-inputs v-else :form="form" :readonly="existUser"></user-create-inputs>
            </div>
        
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title">
                <h4>優惠身分</h4>
                </span> 
            </div>  
            <div class="panel-body">
                <div  class="form-group">
                    
                    <div class="col-md-12">
                        <check-box-list :options="identity_options" :default_values="form.identityIds" 
                            @select-changed="onIdentitiesChanged">
                        </check-box-list>
                        <small class="text-danger" v-if="form.errors.has('identityIds')" v-text="form.errors.get('identityIds')"></small>
                    </div>
                   
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">持中國信託蓮花卡繳費</label>
                    <div class="col-md-10">
                        <toggle :items="lotusOptions"   :default_val="form.lotus" @selected="setLotus"></toggle>
                    </div>
                    
                </div>
            </div>
        
        </div>
        
        
    </div>
    
</template>

<script>
import Details from './detail-view';
import UserInputs from '../user/create-inputs';
import UserView from '../user/view';
export default {
    name: 'SignupCreateInputs',
    components: {
        'user-create-inputs':UserInputs,
        'user-view':UserView,
        'signup-details':Details
    },
    props: {
        form: {
            type: Object,
            default: null
        },
        center_options: {
            type: Array,
            default: null
        },
        identity_options: {
            type: Array,
            default: null
        },
    },
    data(){
		return {
            lotusOptions:Helper.boolOptions(),
            userSettings:{
                role:'Student',
                can_edit:true,
                can_back:true,
                can_delete:false
                   
           },
		}
	},
    computed:{
		canEditCenters(){
            if(!this.center_options) return false;
            return this.center_options.length > 1;
        },
        existUser(){
           
            if(!this.form) return false;
            if(!this.form.user) return false;
            if(!this.form.user.id) return false;

            return parseInt(this.form.user.id) > 0;
        }
        
    },
    methods:{
        onAddDetail(){
            this.$emit('add-detail');
        },
        setDate(val){
            this.form.signup.date=val;
        },
        onRemoveDetail(item){
            let courseId=item.courseId;
            let index=this.form.signup.details.findIndex((detail) =>{
                return detail.courseId==courseId;
            });
            this.form.signup.details.splice(index, 1);

           
            this.form.courseIds.splice(this.form.courseIds.indexOf(courseId),1);
		
        },
        onIdentitiesChanged(values){
            this.form.identityIds=values.slice(0);
            
        },
        setLotus(val){
            this.form.lotus=val;
        },
        resetUser(){
            this.$emit('reset-user')
        },
        onUserUpdated(){
            
            this.$emit('user-saved', this.form.user.id);
        }

    }
}
</script>

