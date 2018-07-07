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
                <div v-if="editting">
                    <button @click.prevent="onSubmit" class="btn btn-success">
                        <i class="fa fa-save"></i>
						存檔
					</button>
                    <button @click.prevent="cancelEdit" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-circle-left"></i>
                        返回
                    </button>
                </div>
                <div v-else>
                    
                    <button v-show="can_edit" @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                        <i class="fa fa-edit"></i>  
                        編輯
                    </button>
                </div>
                <small class="text-danger" v-if="err_msg" v-text="err_msg"></small>
            </div>
        </div>  
        <div class="panel-body">
            <table  class="table">
                <thead>
                    <tr>
                        <th style="width:5%">
                            順序
                        </th>
                        <th style="width:45%">
                            標題
                        </th>
                        <th>
                            說明
                        </th>
                    </tr>
                </thead>
                <tbody>
                    
                    <row v-for="(item,index) in processes" :key="index"
                      :model="item" 
                      :edit="editting" >
                    </row>
                </tbody>
            
            </table>
        </div>
        
    </div>
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
            course: {
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

                processes:[],

                editting:false,

                form:new Form({
                    processes:[]
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
               
                this.editting=false;
                this.processes=[];

                for(let i=1; i<=12; i++){
                    let exist=this.course.processes.find(processItem=>{
                        return parseInt(processItem.order)==i;
                    })
                    let item={
                        id:0,
                        order:i,
                        title:'',
                        content:'',
                    };

                    if(exist){
                        item.id=exist.id;
                        item.title=exist.title;
                        item.content=exist.content;
                    }
                    this.processes.push(item);
                }
               
            },
            loadProcesses(){

            },
            beginEdit(){
                this.editting=true;
                
            },
            cancelEdit(){
                this.init();
            },
            onEditCanceled(){
                this.init();
            },
            onSubmit(){
               
                this.submitting=true;
                let form = new Form({
                    course_id:this.course.id,
                    processes:this.processes.slice(0)
                });
                
               
                let save = Process.store(form);

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

            }
           
            
        }
    }
</script>
