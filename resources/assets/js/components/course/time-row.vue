<template>
    <tr v-if="edit">
        <td style="width:80%;white-space:nowrap;">
            <form class="form" @submit.prevent="onSubmit" @keydown="clearErrorMsg($event.target.name)">
                <table style="width:95%;">
                    <tr>
                        <td style="width:30%;">
                            <select v-model="form.weekdayId" class="form-control" style="width:90%">
                                <option v-for="(item,index) in weekdays" :key="index"  :value="item.value" v-text="item.text"></option>
                            </select>
                        </td>
                        
                        <td style="width:35%;">
                            <input type="text" name="on" class="form-control" v-model="form.on" style="width:90%">
                            <small class="text-danger" v-if="form.errors.has('on')" v-text="form.errors.get('on')"></small>
                        </td>

                        <td style="width:35%;">
                            <input type="text" name="off" class="form-control" v-model="form.off" style="width:90%">
                            <small class="text-danger" v-if="form.errors.has('off')" v-text="form.errors.get('off')"></small>
                        </td>
                    </tr>
                </table>
            </form>
            
        </td>
        <td>
            <button  @click.prevent="onSubmit" class="btn btn-success btn-sm" >
                <i class="fa fa-save"></i>
            </button>
            <button @click.prevent="onCancel" class="btn btn-sm btn-default">
                <i class="fa fa-undo"></i> 
            </button>
        </td>
    </tr>
    <tr v-else>
        <td style="width:80%">
            {{ model.weekday.title }}  &nbsp;
            {{ model.timeString }}
        </td>
        <td>
            <button v-if="can_edit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                <i class="fa fa-edit"></i> 
            </button>
            <button @click.prevent="onRemove" class="btn btn-sm btn-danger">
                <i class="fa fa-trash"></i> 
            </button>
        </td>
    </tr>
</template>
<script>
    export default {
        name:'ClassTimeRow',
        props: {
            form: {
              type: Object,
              default: null
            },
            weekdays:{
               type: Array,
               default: null
            },
            model:{
               type: Object,
               default: null
            },
            edit:{
               type: Boolean,
               default: false
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
            beginCreate(){

            },
            beginEdit() {
                this.$emit('edit',this.model);
            },
            onCancel(){
                this.$emit('cancel');
            },
            onSubmit(){
                this.$emit('submit');
            },
            onRemove(){
                 this.$emit('delete',this.model);
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
                
        }
    }
</script>
