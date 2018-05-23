<template>
    <tr v-if="edit">
        <td>
          
           
        </td>
        <td> {{ model.course.number }} {{ model.course.fullName }}</td>
        <td v-if="can_percents">
            <select v-model="model.percents" class="form-control">
                <option v-for="(item,index) in percents_options" :key="index"  :value="item.value" v-text="item.text"></option>
                
            </select>
        </td>
        <td>
            <input type="text" name="ps" class="form-control" v-model="model.ps">
            
        </td>
       
        
        <td>
            <button  @click.prevent="onSubmit" class="btn btn-success btn-xs" >
                <i class="fa fa-save"></i>
            </button>
            <button @click.prevent="onCancel" class="btn btn-xs btn-default">
                <i class="fa fa-undo"></i> 
            </button>
        </td>
    </tr>
    <tr v-else>
        <td v-if="can_edit">
            <button   @click.prevent="removeDetail" class="btn btn-danger btn-xs" >
                <i class="fa fa-trash"></i> 
            </button>
           
        </td>
        <td> {{ model.course.number }} {{ model.course.fullName }}</td>
        <td v-if="can_percents"> {{ model.percents }}</td>
        <td v-if="!can_edit" >
            {{ model.tuition | formatMoney }}
        </td>
        <td> {{ model.ps }}</td>    
        <td>
            <button v-if="can_edit"  @click.prevent="beginEdit" class="btn btn-primary btn-xs" >
                <i class="fa fa-edit"></i> 
            </button>
           
        </td>
        
    </tr>
</template>
<script>
    export default {
        name:'QuitRow',
        props: {
            index:{
               type: Number,
               default: 0
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
            can_percents:{
               type: Boolean,
               default: false
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
            removeDetail(){
                 this.$emit('remove-row', this.index);
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
