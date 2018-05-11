<template>
<div>
    <div v-if="center" class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div>
                <button v-show="can_back"  @click="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="center.canEdit" v-show="can_edit" @click="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
                <button v-if="center.canDelete"  @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
            </div>
        </div> 
        <show v-if="readOnly"  :center="center">  
        </show>
        <edit v-else :id="id" 
           @saved="onSaved"   @canceled="onEditCanceled" >                 
        </edit>
    </div>

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="deleteConfirm.show=false" @confirmed="deleteCenter">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from './show.vue'
    import Edit from './edit.vue'
    export default {
        name:'Center',
        components: {
            Show,
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
                icon:Menus.getIcon('Centers') ,
                readOnly:true,

                center:null,

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
                let text='開課中心';
               
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
                this.fetchData();
                this.readOnly=true;

                this.deleteConfirm={
                    id:0,
                    show:false,
                    msg:''
                }; 
            },
            fetchData() {
               
                let getData=Center.show(this.id);
               
                getData.then(center => {
                   
                    this.center = {
                        ...center
                    }; 
                    this.$emit('loaded',this.center);
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            }, 
             
            onBack(){
                this.$emit('back');
            },
            beginEdit() {
                this.readOnly=false;
            },
            onEditCanceled(){
                this.init();
            },
            beginDelete(){
                let name=this.center.name;
                let id= this.center.id;

                this.deleteConfirm.msg='確定要刪除 ' + name + ' 嗎?'
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;                
            },
            deleteCenter(){
             
                let id = this.deleteConfirm.id; 
                let remove=Center.remove(id);
                
                this.deleteConfirm.show=false;

                remove.then(() => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })

                   
                
            },
            onSaved(){
                this.init();
            }  
        }
    }
</script>
