<template>

<div v-if="form">
    
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">                           
                <label>角色</label>
                <div>
                    <toggle :items="role_options"   :default_val="form.role" @selected="setRole"></toggle>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            
        </div>
    </div> <!--  row   -->
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">                           
                <label>所屬中心</label>
                <check-box-list :options="center_options" :default_values="form.centerIds" 
					@select-changed="onCentersChanged">
				</check-box-list>
                <small class="text-danger" v-if="form.errors.has('centerIds')" v-text="form.errors.get('centerIds')"></small>
            </div>
        </div>
       
    </div> <!--  row   -->
    
    
</div>  
</template>


<script>
    export default {
        name: 'AdminInputs',
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
        data() {
            return {
                
               
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
            onCanceled(){
                this.$emit('canceled')
            },
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