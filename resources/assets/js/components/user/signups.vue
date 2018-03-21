<template>
    <signup-table :signups="signups">
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
        }
    },
    data(){
        return{

            signups:[],
           
        }
    },
    computed:{
        
    },
    beforeMount(){
        this.init()
    },
    methods:{
        init(){
            this.fetchData();
        },
        fetchData() {
            let getData=Signup.getByUser(this.user.id);
            
            getData.then(signups => {
                this.signups = signups.slice(0);
            })
            .catch(error=> {
              
                Helper.BusEmitError(error);
            })
        }
        
    }
    
}
</script>

