<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="lesson" >
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="canEdit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
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

            <show v-if="readOnly"  :lesson="lesson" @edit-review="onEditReview" @edit-ps="onEditPS" >  
            </show> 
            <edit v-else ref="editComponent"  :id="id" 
                @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
            
        </div>
        
    </div>
    <review-editor :showing="reviewEditor.show" :reviewed="reviewEditor.reviewed"
      @close="reviewEditor.show=false" @save="updateReview">
    </review-editor>

    <delete-confirm  :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteLesson">        
    </delete-confirm>
   
</div>
</template>
<script>
   

    import Show from './show.vue';
    import Edit from './edit.vue';
    
   
    export default {
        name:'Lesson',
        components: {
            Show,
            Edit,
           
        },
        props: {
            id: {
              type: Number,
              default: 0
            },
            course_id: {
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
                icon:Menus.getIcon('lessons') ,
                readOnly:true,

                lesson:null,


                reviewEditor:{
                    show:false,
                    id:0,
                    reviewed:false,
                },

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },

                
            }
        },
        computed:{
            creating(){
                if(this.readOnly) return false;
                if(this.id)  return false;
                return true;
            },
            canEdit(){
                if(!this.can_edit) return false;
                if(!this.readOnly) return false;
                if(!this.lesson) return false;
                
                return this.lesson.canEdit;
            },
            canDelete(){
                if(!this.canEdit) return false;
                return this.lesson.canDelete;
            },
            title(){
               
                if(this.readOnly) return this.icon + ' 課堂紀錄';
                if(this.creating) return this.icon + ' 新增報名';
               
                return this.icon + ' 編輯課堂紀錄';
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
              
                let getData=Lesson.show(this.id);
               
                getData.then(lesson => {
                   
                    this.lesson = {
                        ...lesson
                    }; 

                    this.$emit('loaded',this.lesson);
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
            onEditReview(){
                this.reviewEditor.id=this.lesson.id;
                this.reviewEditor.reviewed=Helper.isTrue(this.lesson.reviewed);
                this.reviewEditor.show=true;
            },
            updateReview(reviewed){
                
                let lessons= [{
                    id:this.reviewEditor.id,
                    reviewed:reviewed
                }];

                let form=new Form({
                    lessons:lessons
                });

                let save=Lesson.review(form);
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
               
                this.psEditor.id=this.lesson.id;
                this.psEditor.text=this.lesson.ps;
               
                this.$refs.psEditor.init(this.lesson.ps);

                this.psEditor.show=true;
            },
            updatePS(ps){
               
                let form=new Form({
                     id:this.psEditor.id,
                     ps:ps
                });

                let save=Lesson.updatePS(form);
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
            onSaved(){
                this.init();
            },
            beginDelete(){
                
                let id=this.lesson.id;
                this.deleteConfirm.msg=`確定要刪除這筆課堂紀錄嗎?`;
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;       
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deleteLesson(){
                this.closeConfirm();
                
                let id = this.deleteConfirm.id ;
                let remove= Lesson.remove(id);
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
