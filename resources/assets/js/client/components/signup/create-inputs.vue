<template>
    <div v-if="form">
     
        <signup-details text="報名課程" :model="form.signup" >

        </signup-details>
        
        <div style="padding-top:1.2em">
        <div class="panel" >
            <div class="panel-heading panel-title heading" >
                學員資料
            </div>
            <div class="panel-block">
               <profile-inputs :form="form"></profile-inputs>
		    </div>
   

        </div>
        </div>
    </div>
    
</template>

<script>
import Details from './detail-view';
import ProfileInputs from '../profile/inputs';
export default {
    name: 'SignupCreateInputs',
    components: {
        'profile-inputs':ProfileInputs,
        'signup-details':Details
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
            lotusOptions:Helper.boolOptions(),
            userSettings:{
                title_text:'學員資料',
                role:'Student',
                can_edit:true,
                can_back:true,
                can_delete:false
                   
           },
		}
	},
    computed:{
		
       
        
    },
    methods:{
        
        onIdentitiesChanged(values){
            this.form.identityIds=values.slice(0);
            
        },
        setLotus(val){
            this.form.lotus=val;
        }

    }
}
</script>

