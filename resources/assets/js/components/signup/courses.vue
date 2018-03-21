<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="course" v-show="readOnly">
                
                
                <a  v-if="can_edit" @click.prevent="onCreate" href="#" class="btn btn-primary">
                   <i class="fa fa-plus-circle"></i> 新增
                </a>
               
            </div>
        </div>  
        <div class="panel-body">
            
        </div>
        
    </div>
    
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteCourse">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from './show.vue';
    import Edit from './edit.vue';
    export default {
        name:'Course',
        components: {
            Show,
            Edit,
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            group:{
               type: Boolean,
               default: false
            }, 
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            hide_delete:{
              type: Boolean,
              default: false
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {
                icon:Menus.getIcon('courses') ,
                readOnly:true,

                course:null,

                reviewEditor:{
                    show:false,
                    id:0,
                    reviewed:false,
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            creating(){
                if(this.readOnly) return false;
                if(this.id)  return false;
                return true;
            },
            title(){
                let text='課程資料';
               
                if(this.readOnly) return this.icon + ' ' + text;
                if(this.creating) return this.icon + ' 新增' + text;

                return this.icon + ' 編輯'+ text;
                
            }
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
            'version':'init'
        },
        methods: {
            init() {
                if(this.id){
                    this.fetchData();
                    this.readOnly=true;
                }else{
                    this.readOnly=false;                    
                }
                

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }; 
            },
            fetchData() {
               
                let getData=Course.show(this.id);
               
                getData.then(course => {
                   
                    this.course = {
                        ...course
                    }; 

                    this.$emit('loaded',this.course);
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            }, 
            onCreate(){

            },
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.init();
            },
            onSaved(){
                
                this.init();
            },  
            beginDelete(){
                
                let name=  this.course.fullName;
                let id=this.course.id;
 
                this.deleteConfirm.msg='確定要刪除課程 ' + name + ' 嗎？'
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deleteCourse(){
                this.closeConfirm();
                
                let id = this.deleteConfirm.id;
                let remove= Course.remove(id);
                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.$emit('deleted');
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.closeConfirm();   
                })
            },

            
        }
    }
</script>
