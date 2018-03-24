<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="panel-title">
                <h4 v-html="title"></h4>
                </span> 
                
                <div v-if="readOnly">
                    <button v-if="canCreate" @click="beginCreate" class="btn btn-primary btn-sm" >
                        <i class="fa fa-plus"></i> 新增
                    </button>
                    <button v-if="canEdit && contactInfoId>0" @click="beginEdit" class="btn btn-primary btn-sm" >
                        <i class="fa fa-edit"></i> 編輯
                    </button>
                    <button v-if="canDelete" v-show="can_edit" @click="beginDelete" class="btn btn-danger btn-sm" >
                        <i class="fa fa-trash"></i>  刪除
                    </button>

                    
                
                </div>
            </div>  <!-- End panel-heading-->
            <div class="panel-body">
                <show v-if="readOnly"  :model="contactInfo" >  
                </show>
                <edit v-else :id="contactInfoId" :can_options="can_options"
                    :user_id="userId"  :center_id="centerId"
                    @saved="onSaved"   @canceled="onEditCanceled" >   
                                 
                </edit>
            </div>  <!-- End panel-body-->
        </div>
        
        <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
            @close="closeConfirm" @confirmed="deleteContactInfo">        
        </delete-confirm>
    </div>
</template>
<script>
    import Show from './show.vue';
    import Edit from './edit.vue'


    export default {
        name:'ContactInfo',
        components: {
            Show,
            Edit,
          
        },
        props: {
            type:{
                type: String,
                default: 'center'
            },
            model:{
                type: Object,
                default: null
            },
            can_edit:{
                type: Boolean,
                default: true
            },
            can_options:{
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                title:Menus.getIcon('ContactInfo') + '  聯絡資訊', 
                readOnly:true,

                contactInfo:null,

                centerId:0,
                userId:'',

                deleteConfirm:{
                    id:this.contactInfoId,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            canEdit(){
                if(!this.can_edit) return false;
                if(!this.contactInfo) return false;
                return this.contactInfo.canEdit;
            },
            canCreate(){
                if(!this.can_edit) return false;
                return this.contactInfoId < 1;
            },
            canDelete(){
                if(!this.canEdit) return false;
                return this.contactInfoId > 0;
            },
            contactInfoId(){
                if(this.contactInfo) return this.contactInfo.id;
                return 0;
            }
           
        }, 
        beforeMount(){
            this.init()
        },
        watch: {
            'id': 'init',
        },
        methods: {
            init() {
                this.readOnly=true;

                if(this.model.contactInfo){
                    this.contactInfo={
                        ...this.model.contactInfo
                    };
                }

                if(this.type.toLowerCase() =='center') 
                {
                    this.centerId=this.model.id;
                }else{
                    this.userId=this.model.id;
                }
            },
            fetchData(){
               
                let id=this.contactInfoId;
                if(!id) return;
                let getData =ContactInfo.show(id);
                getData.then(data => {
                    this.contactInfo = {
                        ...data
                    };
                    if(this.model.canEdit) this.contactInfo.canEdit = true;
                    else this.contactInfo.canEdit = false;
                    
                                      
                })
                .catch(error=> {
                    Helper.BusEmitError(error);
                })
              
            },
            beginEdit() {
                this.readOnly=false;
            },
            beginCreate(){
                this.readOnly=false;
            },
            onEditCanceled(){
                this.readOnly=true;
            },
            onSaved(){
                if(this.contactInfoId){
                    this.fetchData();
                    this.readOnly=true;
                }else{
                    this.$emit('created')
                }
                
            },
            beginDelete(){
                let id=this.contactInfo.id
                this.deleteConfirm.msg='確定要刪除聯絡資訊嗎？'
                this.deleteConfirm.id=id
                this.deleteConfirm.show=true                
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deleteContactInfo(){
                let id = this.deleteConfirm.id; 
                let remove= ContactInfo.delete(id);
                remove.then(result => {
                    Helper.BusEmitOK('刪除成功');
                   
                    this.deleteConfirm.show=false;
                    this.contactInfo=null;     
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
