<template>
    <tr v-if="edit" @keydown="clearErrorMsg($event.target.name)">
        
        
        
        <td v-if="false">
            <toggle :items="boolOptions"   :default_val="form.member" @selected="setMember"></toggle>
        </td>
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
    <tr v-else>
        
       
        <td v-if="false">
            <i v-if="isTrue(identity.member)" class="fa fa-check-circle" style="color:blue"></i>
        </td>
        
         <td> {{  identity.name }}  </td>
        <td>
            <button v-if="canEdit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                <i class="fa fa-edit"></i> 
            </button>
         
            
        </td>
       
    </tr>
</template>
<script>
    export default {
        name:'IdentityRow',
        props: {
            form: {
              type: Object,
              default: null
            },
            identity:{
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
                boolOptions:Helper.boolOptions(),
            }
        },
        computed:{
            canEdit(){
                if(!this.can_edit) return false;
                if(this.identity.code=='special') return false;
                return true;
            }
           
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
                this.$emit('edit',this.identity);
            },
            onCancel(){
                this.$emit('cancel');
            },
            setMember(val){
                this.form.member=val;
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
