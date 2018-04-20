<template>
    <!-- <div>
        <div class="columns">
            <h2 class="title">
                報名紀錄
            </h2>
        </div>
        <div style="padding-top:1em">
            
            <show :signup="signup" >
            </show>
        </div>
    </div> -->
        
    <div>
        <div class="panel">
            <div class="panel-heading panel-title heading" >
                報名紀錄
            </div>
            <div class="panel-block">
                <show :signup="signup" >
                </show>
		    </div>
   

        </div>
    </div>  
</template>


<script>
   
    import Show from '../../components/signup/show.vue';
    export default{
        name:'SignupShowView',
        components: {
            Show           
        },
        props: {
            signup: {
                type: Object,
                default: null
            }
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
            
            
        },



    }
</script>
