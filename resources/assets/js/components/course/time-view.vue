<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">
               <h4 v-html="title"></h4>
            </span> 
            <div v-if="course" v-show="!creating">
                
                <button v-if="canCreate" @click.prevent="beginCreate" class="btn btn-primary btn-sm" >
                    <i class="fa fa-plus-circle"></i> 
                    新增
                </button>
               
            </div>
        </div>  
        <div class="panel-body">
            <table  class="table show-data" style="width: 600px;">
                <tbody>
                    <row  v-if="creating" :form="form"   :edit="true" :weekdays="weekdays"
                       @cancel="creating=false" @submit="onSubmit">
                    </row>
                    <row v-for="(item,index) in course.classTimes" :key="index"
                      :can_edit="can_edit" :model="item" :form="form"  
                      :edit="form.id==item.id" :weekdays="weekdays"
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
    import Row from './time-row.vue'
    export default {
        name:'ClassTime',
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
            },
            weekdays:{
               type: Array,
               default: null
            },
        },
        data() {
            return {
                icon:Menus.getIcon('classtimes') ,
                course:null,
                creating:false,

                form:new Form({
                    id:0,
                    weekdayId:0,
                    on:1600,
                    off:1800
                }),

                deleteConfirm:{
                    id:0,
                    show:false,
                    msg:'',

                }
            }
        },
        computed:{
            canCreate(){

                if(!this.can_edit) return false;
                if(this.creating) return false;
                if(this.form.id>0) return false;
                return true;
            },
            title(){
                let text='上課時間';

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
                    courseId:this.course.id,
                    weekdayId:this.weekdays[0].value,
                    on:1600,
                    off:1800,
                });
                this.creating=true;
            },
            beginEdit(classtime) {
                this.form=new Form({
                    id:classtime.id,
                    weekdayId:classtime.weekdayId,
                    on:classtime.on,
                    off:classtime.off,
                });
            },
            onEditCanceled(){
                this.init();
            },
            onSubmit(){
                this.form.on=this.form.on.toString().replace(':','');
                this.form.off=this.form.off.toString().replace(':','');

                let save=null;
                if(this.form.id < 1) save=ClassTime.store(this.form);
                else save=ClassTime.update(this.form.id,this.form);

                save.then(() => {
                        this.$emit('saved');
                        Helper.BusEmitOK('資料已存檔');
                    })
                    .catch(error => {
                       
                        Helper.BusEmitError(error,'存檔失敗');
                    })
            },
            onDelete(classtime){
             
                let name=classtime.weekday.title + ' ' + classtime.timeString;
                let id=classtime.id;

                this.deleteConfirm.msg='確定要刪除 ' + name + ' 嗎？'
                this.deleteConfirm.id=id
                this.deleteConfirm.show=true   
            },
            closeConfirm(){
                this.deleteConfirm.show=false
            },
            deleteCourse(){
                this.closeConfirm();

                let id = this.deleteConfirm.id;
                let remove= ClassTime.remove(id);
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
