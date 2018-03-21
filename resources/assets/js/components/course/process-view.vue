<template>
<div v-if="course">
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="submitting">
                <button class="btn btn-default">
                    <i class="fa fa-spinner fa-spin"></i> 
                    處理中
                </button>
            </div>
            <div v-else>
                
                <button v-if="canCreate" @click.prevent="beginCreate" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus-circle"></i> 
                    新增
                </button>
               
                <label v-if="showImportBtn" @click="beginImport" href="#" class="btn btn-warning btn-sm btn-file" >
                    <i class="fa fa-upload"></i>
                    匯入
                    <input  type="file"  ref="fileinput"  name="import_file" style="display: none;"  
                       @change="onFileChange" >
                </label>
                <small class="text-danger" v-if="err_msg" v-text="err_msg"></small>
            </div>
        </div>  
        <div class="panel-body">
            <table  class="table">
                <thead>
                    <tr>
                        <th style="width:8%">
                            順序
                        </th>
                        <th style="width:25%">
                            標題
                        </th>
                        <th>
                            說明
                        </th>
                        <th style="width:8%"></th>
                    </tr>
                </thead>
                <tbody>
                    <row  v-if="creating" :form="form"   :edit="true"  :orders="orders"
                       @cancel="creating=false" @submit="onSubmit">
                    </row>
                    <row v-for="(item,index) in course.processes" :key="index"
                      :can_edit="can_edit" :model="item" :form="form"  
                      :edit="form.id==item.id" :orders="orders"
                      @edit="beginEdit" @cancel="form.id=0" @submit="onSubmit"
                      @delete="onDelete">
                    </row>
                </tbody>
            
            </table>
        </div>
        
    </div>

    <delete-confirm :showing="deleteConfirm.show" :message="deleteConfirm.msg"
      @close="closeConfirm" @confirmed="deleteCourse">        
    </delete-confirm>
</div>
</template>
<script>
    import Row from './process-row.vue';
    export default {
        name:'Process',
        components: {
            Row
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
                icon:Menus.getIcon('process') ,
                course:null,
                creating:false,

                orders:Helper.numberOptions(1, 30),

                form:new Form({
                    id:0,
                    courseId:0,
                    title:'',
                    content:''
                }),

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                },

                files: [],
                submitting:false,
                err_msg:''
            }
        },
        computed:{
            canCreate(){
                if(!this.can_edit) return false;
                if(this.creating) return false;
                if(this.form.id>0) return false;
                return true;
            },
            showImportBtn(){
                if(!this.canCreate) return false;
                if(this.course.processes && this.course.processes.length) return false;
                return true;
            },
            title(){
                let text='教學大綱';

                return this.icon + ' '+ text;
                
            }
           
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
                this.course={
                    ...this.model
                };
                this.creating=false;
               
            },
            beginCreate(){
                this.form=new Form({
                    id:0,
                    order:1,
                    courseId:this.course.id,
                    title:'',
                    content:''
                });
                this.creating=true;
            },
            beginImport(){
                this.$refs.fileinput.value = null;
                this.err_msg='';
            },
            onFileChange(e) {
               
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)  return;
                   
                this.files = e.target.files;

                this.submitImport();
            },
            beginEdit(item) {
                this.form=new Form({
                    id:item.id,
                    order:item.order,
                    courseId:this.course.id,
                    title:item.title,
                    content:item.content
                });
            },
            onEditCanceled(){
                this.init();
            },
            onSubmit(){
                this.submitting=true;
                let save=null;
                if(this.form.id < 1) save=Process.store(this.form);
                else save=Process.update(this.form.id,this.form);

                save.then(() => {
                        this.$emit('saved');
                        Helper.BusEmitOK('資料已存檔');
                        this.submitting=false;
                    })
                    .catch(error => {
                        this.submitting=false;
                        Helper.BusEmitError(error,'存檔失敗');
                    })
            },
            submitImport(){
                this.submitting=true;
                let form = new FormData();
                for (let i = 0; i < this.files.length; i++) {
                    form.append('file', this.files[i]);                    
                }

                let store=Process.importByCourse(this.course.id,form);
                store.then(result => {
                       
                        Helper.BusEmitOK();
                        this.submitting=false;
                        this.$emit('saved');
                    })
                    .catch(error => {
                        
                        let msg =Helper.getErrorMsg(error);
                        if(msg){
                            this.err_msg=msg;
                            
                        }else{
                            Helper.BusEmitError(error);
                        }

                        this.submitting=false
                    })

            },
            onDelete(item){
             
                let name=item.order + ' ' + item.title;
                let id=item.id;

                this.deleteConfirm.msg='確定要刪除 ' + name + ' 嗎?';
                this.deleteConfirm.id=id;
                this.deleteConfirm.show=true;   
            },
            closeConfirm(){
                this.deleteConfirm.show=false;
            },
            deleteCourse(){
                this.closeConfirm();

                let id = this.deleteConfirm.id;
                let remove= Process.remove(id);
                remove.then(() => {
                    Helper.BusEmitOK('刪除成功');
                    this.$emit('saved');
                })
                .catch(error => {
                    Helper.BusEmitError(error,'刪除失敗');
                    this.closeConfirm(); 
                })

                 
            },
            
        }
    }
</script>
