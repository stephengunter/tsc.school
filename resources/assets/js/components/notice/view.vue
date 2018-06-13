<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div>
                <button v-show="can_back"  @click="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                
                <button v-if="canDelete"  @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
            </div>
        </div> 
        <div class="panel-body">
            <edit :id="id" @loaded="onLoaded"
            @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div> 
    </div>

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="deleteConfirm.show=false" @confirmed="deleteNotice">        
    </delete-confirm>
</div>
</template>
<script>
   
    import Edit from './edit.vue'
    export default {
        name:'Notice',
        components: {
            Edit,
        },
        props: {
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
                icon:'' ,
                readOnly:true,

                notice:null,

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            creating(){
               
                if(this.id)  return false;
                return true;
            },
            title(){
                let text='公告訊息';
               
               
                if(this.creating) return this.icon + ' 新增' + text;

                return this.icon + ' 編輯'+ text;
                
            },
            canDelete(){
                if(this.creating) return false;
                if(!this.notice) return false;
                return this.notice.canDelete;
            }
           
        },
        beforeMount(){
            this.init()
        },
        watch: {
          
        },
        methods: {
            init() {
                
            },
            onBack(){
                this.$emit('back');
            },
            onLoaded(notice){
                this.notice = {
					...notice
				};
            },
            onEditCanceled(){
                this.$emit('back');
            },
            beginDelete(){
               
                let id= this.notice.id;

                this.deleteConfirm.msg='確定要刪除此篇公告嗎?'
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;                
            },
            deleteNotice(){
             
                let id = this.deleteConfirm.id; 
                let remove=Notice.remove(id);
                
                this.deleteConfirm.show=false;

                remove.then(() => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.deleteConfirm.show=false;  
                })

                   
                
            },
            onSaved(){
                this.$emit('saved');    
            }  
        }
    }
</script>
