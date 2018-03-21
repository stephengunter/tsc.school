<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="course" v-show="readOnly">
                
                <button v-if="canEdit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :course="course" >  
            </show>
            <edit v-else :id="course.id" 
                @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div>
        
    </div>
    
</div>
</template>
<script>
    import Show from './info-show.vue'
    import Edit from './info-edit.vue'
    export default {
        name:'CourseInfo',
        components: {
            Show,
            Edit,
        },
        props: {
            model: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            }
        },
        data() {
            return {
                icon:Menus.getIcon('courses') ,
                course:null,
                readOnly:true,
            }
        },
        computed:{
            canEdit(){

                if(!this.can_edit) return false;
                if(!this.readOnly) return false;
                return true;
            },
            title(){
                let text='課程報名資訊';
               
                if(this.readOnly) return this.icon + ' ' + text;

                return this.icon + ' 編輯'+ text;
                
            }
           
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
                this.readOnly=true;
                this.course={
                    ...this.model
                };
            },
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.init();
            },
            onSaved(){
                this.$emit('saved');
            }
            
        }
    }
</script>
