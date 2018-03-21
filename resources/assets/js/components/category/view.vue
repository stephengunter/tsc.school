<template>
<div>
    <div v-if="center" class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div>
                <button v-show="can_back"  @click="onBack" class="btn btn-default btn-sm" >
                     <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                     返回
                </button>
                <button v-if="center.canEdit" v-show="can_edit" @click="beginEdit" class="btn btn-primary btn-sm" >
                    <span class="glyphicon glyphicon-pencil"></span> 編輯
                </button>
                <button v-if="center.canDelete" v-show="!hide_delete" @click="beginDelete" class="btn btn-danger btn-sm" >
                    <span class="glyphicon glyphicon-trash"></span> 刪除
                </button>
               
            </div>
        </div>  <!-- End panel-heading-->
        <show v-if="readOnly"  :center="center">  
        </show>
        <edit v-else :id="id" 
           @saved="onSaved"   @canceled="onEditCanceled" >                 
        </edit>
    </div>
    <!-- <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteCenter">        
    </delete-confirm> -->
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
                title:Menus.getIcon('Centers') + ' 開課中心',
                readOnly:true,

                center:null,

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
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
            onSaved(){
                this.init();
            },  
            beginDelete(values){
                this.deleteConfirm.msg='確定要刪除 ' + values.name + ' 嗎？'
                this.deleteConfirm.id=values.id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteCenter(){
                this.closeConfirm()
                
                let id = this.deleteConfirm.id 
                let remove= Center.delete(id)
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功')
                    this.$emit('deleted')
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗')
                    this.closeConfirm()   
                })
            },

            
        }
    }
</script>
