<template>
    <tr v-if="edit" @keydown="clearErrorMsg($event.target.name)">
        
        
        <td>
            <input type="text" name="name" class="form-control" v-model="form.name">
            <small class="text-danger" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></small>
        </td>
        <td>
            <toggle :items="boolOptions"   :default_val="form.prove" @selected="setProve"></toggle>
        </td>
        
        <td>
            <select v-model="form.pointOne" class="form-control">
                <option v-for="(item,index) in point_options" :key="index" :value="item.value" v-text="item.text" >
                </option>
            </select>

        </td>
        <td>
            <select v-model="form.pointTwo" class="form-control">
                <option v-for="(item,index) in point_options" :key="index" :value="item.value" v-text="item.text" >
                </option>
            </select>
        </td>
        <td>
            <input type="text" name="ps" class="form-control" v-model="form.ps">
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
        
        <td> {{  discount.name }}  </td>
        <td>
            <i v-if="isTrue(discount.prove)" class="fa fa-check-circle" style="color:red"></i>
        </td>
        <td>
            {{  discount.pointOneText }} 
        </td>
        <td>
            {{  discount.pointTwoText }} 
        </td>
        <td>
            {{  discount.ps }} 
        </td>
        <td>
            <button v-if="can_edit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                <i class="fa fa-edit"></i> 
            </button>
            <button class="btn btn-sm btn-danger"  @click.prevent="onRemove()">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
            
        </td>
       
    </tr>
</template>
<script>
    export default {
        name:'DiscountRow',
        props: {
            form: {
              type: Object,
              default: null
            },
            discount:{
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
            },
            point_options:{
                type: Array,
                default: null
            }
        },
        data() {
            return {
                boolOptions:Helper.boolOptions(),
                pointOptions:''
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
            isTrue(val){
                return Helper.isTrue(val);
            },
            
            beginEdit() {
                this.$emit('edit',this.discount);
            },
            onCancel(){
                this.$emit('cancel');
            },
            setProve(val){
                this.form.prove=val;
            },
            onSubmit(){
                this.$emit('submit');
            },
            onRemove(){
                this.$emit('delete',this.discount);
            },
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
            beginDelete(discount){
                this.$emit('delete' , discount);
            }
                
        }
    }
</script>
