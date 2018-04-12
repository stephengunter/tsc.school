<template>
    <div>
        <div class="columns">
            <h2 class="title">
                線上報名
            </h2>
        </div>
        <div class="columns" style="padding-top:1em">
            <signup-edit :form="form" :identity_options="identity_options"
               @cancel="onCancel">

            </signup-edit>
        </div>
    </div>
        
    
</template>


<script>
    import SignupEdit from '../../components/signup/edit.vue';
    export default{
        name:'SignupEditView',
        components: {
            'signup-edit':SignupEdit        
        },
        props: {
            form: {
                type: Object,
                default: null
            },
            identity_options: {
                type: Array,
                default: null
            },
        },
        data(){
            return {
            
            }
        },
        beforeMount(){
             
        },
        methods: {
            init(){
               
               
            },
            onCancel(){
                
                window.location='/signups';
            },
            onSaved(signup){
               
                this.$emit('saved',signup);    
            }
            
            
        },



    }
</script>
