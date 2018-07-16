<template>
    <signup-table :signups="user.signups" :can_select="can_select">
    </signup-table>
</template>

<script>
import SignupsTable from '../signup/table';
export default {
    name: 'UserSignupRecords',
    components: {
        'signup-table':SignupsTable
    },
    props: {
        user: {
            type: Object,
            default: null
        },
        can_select: {
            type: Boolean,
            default: false
        }
    },
    data(){
        return{

            
           
        }
    },
    computed:{
        
    },
    beforeMount(){
        this.init()
    },
    methods:{
        init(){
            
        }
        
    }
    
}
</script>

