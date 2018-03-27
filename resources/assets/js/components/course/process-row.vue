<template>
    <tr v-if="edit">
      
        <td>
            <select v-model="form.order" class="form-control">
                <option v-for="(item,index) in orders" :key="index"  :value="item.value" v-text="item.text"></option>
               
            </select>
        </td>
        <td>
            <input type="text" name="title" class="form-control" v-model="form.title">
            <small class="text-danger" v-if="form.errors.has('order')" v-text="form.errors.get('order')"></small>              
            <small class="text-danger" v-if="form.errors.has('title')" v-text="form.errors.get('title')"></small>
        </td>
        <td>
            <input type="text" name="content" class="form-control" v-model="form.content">
            <small class="text-danger" v-if="form.errors.has('content')" v-text="form.errors.get('content')"></small>
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
        <td> {{ model.order }}</td>
        <td> {{ model.title }}</td>    
        <td> {{ model.content }}</td>    
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
        name:'ProcessRow',
        props: {
            form: {
              type: Object,
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
            orders:{
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
            beginEdit(item) {
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
