<template>
    <div>
        <div class="columns">
            <h2 class="title">
                線上報名
            </h2>
        </div>
        
        <div class="columns" style="padding-top:1em">
            
            <signup-edit :form="form" :identity_options="identity_options" :center="center"
              :bird_date_text="bird_date_text" @saved="onSaved" @cancel="onCancel">

            </signup-edit>

            
        </div>
       
    </div>
        
    
</template>


<script>
    import SignupEdit from '../../components/signup/edit.vue';
    import ShowSignup from '../../components/signup/show.vue';
    export default{
        name:'SignupEditView',
        components: {
            'show-signup':ShowSignup,
            'signup-edit':SignupEdit,               
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
            center: {
                type: Object,
                default: null
            },
            bird_date_text: {
                type: String,
                default: ''
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
              
                window.location='/signups';
                
            }
            
            
        },



    }
</script>
