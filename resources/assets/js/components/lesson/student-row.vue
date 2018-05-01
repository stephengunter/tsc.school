<template>
    <tr v-if="edit" @keydown="clearErrorMsg($event.target.name)">
        <td>
            <button  @click.prevent="onSubmit" class="btn btn-success btn-sm" >
                <i class="fa fa-save"></i>
            </button>
            <button @click.prevent="onCancel" class="btn btn-sm btn-default">
                <i class="fa fa-undo"></i> 
            </button>
        </td>
        <td>
            <toggle :items="absenceOptions"   :default_val="form.absence" @selected="setAbsence"></toggle>
                    
        </td>
        <td v-text="member.name">
            
        </td>
        <td>
            <input type="text" name="name" class="form-control" v-model="form.ps">
         
        </td>
    </tr>
    <tr v-else>
        <td>
            <button v-if="can_edit"  @click.prevent="beginEdit" class="btn btn-primary btn-xs" >
                <i class="fa fa-edit"></i> 
            </button>
        </td>
        <td>
            <span v-if="absence()" class="label label-default"> 缺席 </span>
        </td>
        <td v-text="member.name">
            
        </td>
        <td v-text="member.ps">
            
        </td>
    </tr>
</template>
<script>
    export default {
        name:'LessonStudentRow',
        props: {
            form: {
              type: Object,
              default: null
            },
            member:{
               type: Object,
               default: null
            },
            index:{
               type: Number,
               default: 0
            },
            edit:{
               type: Boolean,
               default: false
            },
            can_edit:{
               type: Boolean,
               default: true
            },
        },
        data() {
            return {
                absenceOptions:Lesson.absenceOptions()
            }
        },
        computed:{
            
           
        },
        beforeMount(){
            this.init()
        },
        methods: {
            init() {
               
            },
            beginEdit() {
                this.$emit('edit',this.member);
            },
            onCancel(){
                this.$emit('cancel');
            },
            onSubmit(){
                this.$emit('submit');
            },
            absence(){
                return Helper.isTrue(this.member.absence);
            },
            setAbsence(val){
                this.form.absence=val;
            }
                
        }
    }
</script>
