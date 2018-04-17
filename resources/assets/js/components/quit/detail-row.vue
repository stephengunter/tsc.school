<template>
    <tr v-if="edit">
        <td> {{ model.course.number }} {{ model.course.fullName }}</td>
        <td>
            <select v-model="model.percents" class="form-control">
                <option v-for="(item,index) in percents_options" :key="index"  :value="item.value" v-text="item.text"></option>
                
            </select>
        </td>
        <td>
            <input type="text" name="ps" class="form-control" v-model="model.ps">
            
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
        <td> {{ model.course.number }} {{ model.course.fullName }}</td>
        <td> {{ model.percents }}</td>
        <td v-if="!can_edit" >
            {{ model.tuition | formatMoney }}
        </td>
        <td> {{ model.ps }}</td>    
       
        <td>
            <button v-if="can_edit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                <i class="fa fa-edit"></i> 
            </button>
           
        </td>
    </tr>
</template>
<script>
    export default {
        name:'QuitRow',
        props: {
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
            percents_options:{
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
           
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
                
        }
    }
</script>
