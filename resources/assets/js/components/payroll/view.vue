<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="payroll" >
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                
                <button v-if="canDelete" @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
               
            </div>
            <div v-else>
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
            </div>
        </div>  
        <div class="panel-body">

            <show v-if="readOnly"  :payroll="payroll" @edit-review="onEditReview" 
                @edit-ps="onEditPS" @edit-status="onEditStatus">  
            </show> 
            
            
        </div>
        
    </div>

    <review-editor :showing="reviewEditor.show" :reviewed="reviewEditor.reviewed"
      @close="reviewEditor.show=false" @save="updateReview">
    </review-editor>

    <ps-editor ref="psEditor"  :showing="psEditor.show" :text="psEditor.text"
      @close="psEditor.show=false" @save="updatePS">
    </ps-editor>

    <status-editor ref="statusEditor"  :showing="statusEditor.show" :text="statusEditor.text"
      @close="statusEditor.show=false" @save="updateStatus">
    </status-editor>

    <delete-confirm  :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deletePayroll">        
    </delete-confirm>
   
</div>
</template>

<script>
    import Show from './show.vue';
    import StatusEditor from './status-editor.vue';
    export default {
        name:'Payroll',
        components: {
            Show,
            'status-editor':StatusEditor
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
               
                readOnly:true,

                payroll:null,


                reviewEditor:{
                    show:false,
                    id:0,
                    reviewed:false,
                },

                psEditor:{
                    show:false,
                    id:0,
                    text:'',
                },

                statusEditor:{
                    show:false,
                    id:0,
                    text:'',
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },

                
            }
        },
        computed:{
          
            
            canDelete(){
                
                return this.payroll.canDelete;
            },
            title(){
               
                if(this.readOnly) return '教師鐘點費';
                return '';
            },
            
           
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
              
                let getData=Payroll.show(this.id);
               
                getData.then(payroll => {
                   
                    this.payroll = {
                        ...payroll
                    }; 

                    this.$emit('loaded',this.payroll);
                })
                .catch(error=> {
                    this.loaded = false 
                    Helper.BusEmitError(error)
                })
            }, 
            onBack(){
                this.$emit('back');
            },
            onEditReview(){
                this.reviewEditor.id=this.payroll.id;
                this.reviewEditor.reviewed=Helper.isTrue(this.payroll.reviewed);
                this.reviewEditor.show=true;
            },
            updateReview(reviewed){
                
                let payrolls= [{
                    id:this.reviewEditor.id,
                    reviewed:reviewed
                }];

                let form=new Form({
                    payrolls:payrolls
                });

                let save=Payroll.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.fetchData();
                    this.reviewEditor.show=false;
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onEditPS(){
               
                this.psEditor.id=this.payroll.id;
                this.psEditor.text=this.payroll.ps;
               
                this.$refs.psEditor.init(this.payroll.ps);

                this.psEditor.show=true;
            },
            updatePS(ps){
               
                let form=new Form({
                     id:this.psEditor.id,
                     ps:ps
                });

                let save=Payroll.updatePS(form);
				save.then(() => {
                    this.psEditor.show=false;
                    Helper.BusEmitOK('資料已存檔');
                    this.init();
				})
				.catch(error => {
                    this.psEditor.show=false;
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onEditStatus(){
               
                this.statusEditor.id=this.payroll.id;
                this.statusEditor.status=this.payroll.status;
                this.statusEditor.show=true;
            },
            updateStatus(status){
                
                let form=new Form({
                    id:this.statusEditor.id,
                    status:status
                });

                let save=Payroll.updateStatus(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.statusEditor.show=false;
                    this.init();
                   
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onSaved(){
                this.init();
            },
            beginDelete(){
                
                let id=this.payroll.id;
                this.deleteConfirm.msg=`確定要刪除這筆課堂紀錄嗎?`;
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;       
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deletePayroll(){
                this.closeConfirm();
                
                let id = this.deleteConfirm.id ;
                let remove= Payroll.remove(id);
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
