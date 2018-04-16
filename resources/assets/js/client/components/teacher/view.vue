<template>

    <div>
        <div class="panel">
            <div class="panel-heading panel-title heading" >
                
                {{ title }}
                
                <div style="float: right">
                    <a href="#" v-show="readOnly" class="button is-info is-outlined" @click.prevent="beginEdit">
                        <span class="icon is-small" style="vertical-align:middle;">
                            <i class="fa fa-edit"></i> 
                        </span>
                        <span>
                        修改    
                        </span>
                        
                        
                    </a>
                </div>
               
               
                 
            </div>
            <div class="panel-block">
                <show v-if="readOnly"  :teacher="teacher" >  
                </show>
                <edit v-else :id="teacherId" 
                    @saved="onSaved"   @cancel="onEditCanceled" >                 
                </edit>
		    </div>
   

        </div>
    </div>   
  
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'TeacherView',
        components: {
            Show,
            Edit,
        },
        props: {
            model:{
                type: Object,
                default: null
            },
            id: {
                type: Number,
                default: 0
            },
            can_edit:{
                type: Boolean,
                default: true
            },            
            can_back:{
                type: Boolean,
                default: true
            },
            title_text:{
                type:String,
                default:'教師資訊'
            },
            version: {
                type: Number,
                default: 0
            },
        },
        data() {
            return {
                
                readOnly:true,

                teacher:null,
            }
        },
        computed:{
            canEdit(){
                if(!this.teacher) return false;

                if(this.model){
                    return this.can_edit;
                }else{
                    if(!this.can_edit) return false;
                    return this.teacher.canEdit;
                }
            },
            teacherId(){
                if(this.teacher) return this.teacher.userId;
                return 0;
            },
            title(){
               
                if(this.readOnly) return  this.title_text;
                return '編輯' + this.title_text;
            }
           
        },
        beforeMount(){
            if(this.model){
                this.loadModel();
            }else{
                this.fetchData();
            } 
        },
        watch: {
            model: {
                handler: function () {
                
                   this.init();
                },
                deep: true
            },
        },
        methods: {
            init() {

                this.readOnly=true;
                
            },
            loadModel(){
                
                this.teacher={
                    ...this.model
                };
            },
            fetchData() {
              
                this.$emit('fetch');
            }, 
             
            onBack(){
                this.$emit('back');
            },
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.readOnly=true;
            },
            onSaved(){
                this.fetchData();
                this.init();
            }, 

            
        }
    }
</script>
