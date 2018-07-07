<template>
    <tr v-if="edit">
      
        <td> {{ model.order }}</td>
        <td>
            <input type="text" name="title" class="form-control" v-model="model.title">
        </td>
        <td>
            <input type="text" name="content" class="form-control" v-model="model.content">
        </td>
        
    </tr>
    <tr v-else>
        <td> {{ model.order }}</td>
        <td> {{ model.title }}</td>    
        <td> {{ model.content }}</td>    
        
    </tr>
</template>
<script>
    export default {
        name:'ProcessRow',
        props: {
            model:{
               type: Object,
               default: null
            },
            edit:{
               type: Boolean,
               default: false
            }
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
