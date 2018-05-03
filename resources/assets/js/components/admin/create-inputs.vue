<template>
    <div v-if="form">
        <user-create-inputs :form="form"></user-create-inputs>
        
        <div v-if="canEditCenters" class="form-group">
            <label class="col-md-2 control-label">所屬中心</label>
            <div class="col-md-10">
                <check-box-list :options="center_options" :default_values="form.centerIds" 
					@select-changed="onCentersChanged">
				</check-box-list>
                <small class="text-danger" v-if="form.errors.has('centerIds')" v-text="form.errors.get('centerIds')"></small>
            </div>
            
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">角色</label>
            <div class="col-md-10">
                <toggle :items="role_options"   :default_val="form.role" @selected="setRole"></toggle>
            </div>
            
        </div>
    </div>
    
</template>

<script>
import UserInputs from '../user/create-inputs';
export default {
    name: 'AdminCreateInputs',
    components: {
        'user-create-inputs':UserInputs,
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
        role_options: {
            type: Array,
            default: null
        },
    },
    data(){
		return {

            
			
		}
	},
    computed:{
		canEditCenters(){
            if(!this.center_options) return false;
            return this.center_options.length > 1
        },
        
    },
    methods:{
        onCentersChanged(values){
            this.form.centerIds=values.slice(0);
            if(values.length)  this.form.errors.clear('centerIds');
        },
        setRole(val){
            this.form.role=val;
        }
    }
}
</script>

