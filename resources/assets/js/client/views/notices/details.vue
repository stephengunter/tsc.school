<template>
    <show :notice="model" @back="onBack">

    </show>

</template>

<script>
    import ShowNotice from '../../components/notice/show.vue';
    export default {
        name:'NoticeDetailsView',
        components:{
            'show':ShowNotice
        },
        props: {
            init_model: {
                type: Object,
                default: null
            }
        },
        beforeMount(){
            if(this.init_model){
                this.model={...this.init_model };
                
            }  

        },
        mounted(){
            this.$emit('loaded');
        },
        data(){
            return{
                title:'公告訊息',
                model:null,
            }
        },
        computed: {
           
        },
        methods:{
            onBack(){
                window.history.back();
            }
            
        },
        
    }
</script>