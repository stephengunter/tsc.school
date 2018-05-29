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
            <input type="text" name="code" class="form-control" v-model="form.code">
            <small class="text-danger" v-if="form.errors.has('code')" v-text="form.errors.get('code')"></small>
        </td>
        <td>
            <input type="text" name="name" class="form-control" v-model="form.name">
            <small class="text-danger" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></small>
        </td>
        <td>
        </td>
        
        <td>
            
        </td>
        <td>
            
        </td>
    </tr>
    <tr v-else>
        <td>
            <button v-if="can_edit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                <i class="fa fa-edit"></i> 
            </button>
        </td>
        <td>{{  category.code }}</td>
        <td>{{  category.name }}</td>
        <td>
            <span v-if="isTop(category)" class="label label-warning"> 置頂 </span>
        </td>
        
        <td v-if="false" >
            <button class="btn btn-sm btn-default" v-if="can_order" @click.prevent="up(category)">
                <i class="fa fa-arrow-up" aria-hidden="true"></i>
            </button>
            <button class="btn btn-sm btn-default" v-if="can_order"  @click.prevent="down(category)">
                <i class="fa fa-arrow-down" aria-hidden="true"></i>
            </button>
        </td>
        <td>
            <button v-if="can_edit"  @click.prevent="beginDelete(category)" class="btn btn-danger btn-sm" >
                <i class="fa fa-trash"></i> 
            </button>
        </td>
    </tr>
</template>
<script>
    export default {
        name:'CategoryRow',
        props: {
            form: {
              type: Object,
              default: null
            },
            category:{
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
            can_order:{
               type: Boolean,
               default: true
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
            isTop(category){
                return Helper.isTrue(category.top);
            },
            beginEdit() {
                this.$emit('edit',this.category);
            },
            onCancel(){
                this.$emit('cancel');
            },
            onSubmit(){
                this.$emit('submit');
            },
            onRemove(){
                 this.$emit('delete',this.category);
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            up(category){
				
                this.$emit('up' , category, this.index);

            },
            down(category){
                this.$emit('down' , category, this.index);
            },
            beginDelete(category){
                this.$emit('delete' , category);
            }
                
        }
    }
</script>
