<template>
<div v-if="form">
    
    <div>
        <user-create-inputs :form="form">

        </user-create-inputs>
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
        }
    },
    data(){
		return {
           
		}
	},
    computed:{
		
        
    },
    methods:{
        init() {
                
        },
        onCanceled(){
            this.$emit('canceled')
        }
    }
}
</script>

