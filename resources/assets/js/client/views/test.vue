<template>
    <profile-view :model="model"></profile-view>
    
</template>

<script>
import ProfileView from '../components/profile/view.vue'
export default {
    name:'Test',
    components:{
       'profile-view':ProfileView
    },
    props: {
        model: {
            type: Object,
            default: null
        },
        photo: {
            type: Boolean,
            default: false
        },

    },
    data(){
        return {
           courses:[]
        }
    },
    computed: {
        
    },
    beforeMount(){
        
    },
    mounted(){
        this.$emit('loaded');
    },
    methods:{
        onSelected(id){
            
        }
      
    }
}
</script>




