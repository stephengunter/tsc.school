<template>
    <tr @keydown="clearErrorMsg($event.target.name)">
        
        <td>
            <input type="text" name="name" class="form-control" v-model="form.name">
            <small class="text-danger" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></small>
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
    
</template>
<script>
    export default {
        name:'IdentityCreateRow',
        props: {
            form: {
              type: Object,
              default: null
            }
        },
        data() {
            return {
                
            }
        },
        computed:{
            
           
        },
        beforeMount(){
            
        },
        methods: {
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
