<template>
    <profile-view :model="user" ref="userView"
     :can_edit="userSettings.can_edit" :can_back="userSettings.can_back"  >
    </profile-view>  
</template>


<script>
    import ProfileView from '../../components/profile/view.vue';
    export default{
        name:'UserProfileView',
        components: {
            'profile-view':ProfileView           
        },
        props: {
            user: {
               type: Object,
               default: null
            },
            role:{
                type: String,
                default: ''	
            }
        },
        data(){
            return {
               userSettings:{
                   can_edit:true,
                   can_back:false
               }
               
            }
        },
        beforeMount(){
             
        },
        methods: {
            
            
        },



    }
</script>
