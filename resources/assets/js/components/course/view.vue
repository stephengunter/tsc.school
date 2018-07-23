<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="course" v-show="readOnly">
                <button v-show="can_back"  @click.prevent="onBack" class="btn btn-default btn-sm" >
                    <i class="fa fa-arrow-circle-left"></i>
                    返回
                </button>
                <button v-if="course.canEdit" v-show="can_edit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                    <i class="fa fa-edit"></i> 
                    編輯
                </button>
                <button v-if="course.canDelete" v-show="!hide_delete" @click.prevent="beginDelete" class="btn btn-danger btn-sm" >
                    <i class="fa fa-trash"></i> 
                    刪除
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <show v-if="readOnly"  :course="course" @edit-review="onEditReview" @edit-active="onEditActive">  
            </show>
            <edit v-else :id="id" :group="group" :terms="terms" :categories="categories"
            @saved="onSaved"   @cancel="onEditCanceled" >                 
            </edit>
        </div>
        
    </div>
    <review-editor :showing="reviewEditor.show" :reviewed="reviewEditor.reviewed" 
      @close="reviewEditor.show=false" @save="updateReview">
    </review-editor>
    <active-editor v-if="course" :course="course" :showing="activeEditor.show"  :percents_options="percents_options"
      @close="activeEditor.show=false" @active="activeCourse" @shut-down="onShutDown">
    </active-editor>
    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="onConfirmed">        
    </delete-confirm>
</div>
</template>
<script>
    import Show from './show.vue';
    import Edit from './edit.vue';
    import ActiveEditor from './active-editor.vue';
    export default {
        name:'Course',
        components: {
            Show,
            Edit,
            'active-editor':ActiveEditor
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
            terms:{
                type:Array,
                default:null
            },
            categories:{
                type:Array,
                default:null
            },
            percents_options:{
               type: Array,
               default: null
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

                activeEditor:{
                  
                    show:false,
                    id:0,
                    active:false,

                    form:null
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
                    msg:'',
                    type:'delete'
                }; 

                this.activeEditor={
                  
                    show:false,
                    id:0,
                    active:false,
                };

                this.reviewEditor={
                  
                    show:false,
                    id:0,
                    reviewed:false,
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
               
                this.reviewEditor.id=this.course.id;
                this.reviewEditor.reviewed=Helper.isTrue(this.course.reviewed);
                this.reviewEditor.show=true;
            },
            updateReview(reviewed){
                
                let courses= [{
                    id:this.reviewEditor.id,
                    reviewed:reviewed
                }];

                let form=new Form({
                    courses:courses
                });

                let save=Course.review(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.init();
                   
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
            },
            onEditActive(){
                this.activeEditor.id=this.course.id;
                this.activeEditor.active=Helper.isTrue(this.course.active);
                this.activeEditor.show=true;
            },
            
            onShutDown(form){
                this.activeEditor.form=form;
                this.confirmShutdown();              
            },
            activeCourse(){
                
                let courses= [{
                    id:this.activeEditor.id,
                    active:true
                }];

                let form=new Form({
                    courses:courses
                });

                let save = Course.active(form);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
                    this.init();
				})
				.catch(error => {
					Helper.BusEmitError(error,'存檔失敗');
				})
                
            },
            confirmShutdown(){
                let percents= parseInt(this.activeEditor.form.percents);
                let msg='';
                if(percents==100)  msg ='全額退費';
                else{
                    let option=this.percents_options.find(item=>{
                        return item.value == this.activeEditor.form.percents;
                    })
                    msg=option.text + '退費';
                }
                
                
                
                let id=this.course.id;
                this.deleteConfirm.type='shutdown';
                this.deleteConfirm.msg='確定要停開此課程並' + msg  + '嗎?';
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;
            },
            onConfirmed(){
                if(this.deleteConfirm.type=='shutdown') this.shutdownCourse();
                else this.deleteCourse();
            },
            shutdownCourse(){

                let form=this.activeEditor.form;
                let id=this.course.id;
                let save = Course.shutdown(form,id);
				save.then(() => {
                    Helper.BusEmitOK('資料已存檔');
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
                
                let name=  this.course.fullName;
                let id=this.course.id;
                this.deleteConfirm.type='delete';
                this.deleteConfirm.msg='確定要刪除課程 ' + name + ' 嗎?';
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
