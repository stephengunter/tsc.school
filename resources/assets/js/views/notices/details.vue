<template>
    <div>
        <notice :id="id" ref="noticeView" 
        :can_edit="noticeSettings.can_edit" :can_back="noticeSettings.can_back"  
        @loaded="onNoticeLoaded"   @back="onBack" @saved="onNoticeSaved"  
        @deleted="onNoticeDeleted" >
        
        </notice>

    </div>    
</template>
<script>
   
    import NoticeComponent from '../../components/notice/view.vue';
    
    export default {
        name: 'NoticeDetails',
        components: {
            'notice':NoticeComponent,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            mode:{
              type: String,
              default: ''
            },
            can_back: {
              type: Boolean,
              default: true
            },
            version:{
               type: Number,
               default: 0
            }
        },
        data(){
            return{
                notice:null,

                noticeSettings:{
                    can_edit:true,
                    can_back:true,
                 
                },
               
            }
        },
        computed:{
           
            
        },
        beforeMount(){
           
        },
        watch: {
            'id': 'init'
        },
        methods:{
            init(){
                
            },
            onNoticeLoaded(notice){
                
                this.notice={
                    ...notice
                };
                
            },
            reloadNotice(){
              
                this.notice=null;
                this.$refs.noticeView.init();
            },
            onNoticeSaved(){  
                this.$emit('saved');   
            },  
            onNoticeDeleted(){
                this.$emit('deleted'); 
            },
            onBack(){
                this.$emit('back');
            },
            
        }
        
    }
</script>
