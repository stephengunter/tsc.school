<template>
<div v-if="form">
    
    <div>
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
        }
    }
}
</script>

