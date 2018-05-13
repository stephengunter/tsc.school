<template>
    <tr v-if="edit" @keydown="clearErrorMsg($event.target.name)">
        
        
        <td>
            <input type="text" name="name" class="form-control" v-model="form.name">
            <small class="text-danger" v-if="form.errors.has('name')" v-text="form.errors.get('name')"></small>
        </td>
        <td>
            <input type="text" name="small_day" class="form-control" v-model="form.small_day">
            <small class="text-danger" v-if="form.errors.has('small_day')" v-text="form.errors.get('small_day')"></small>
        </td>
        <td>
            <input type="text" name="small_night" class="form-control" v-model="form.small_night">
            <small class="text-danger" v-if="form.errors.has('small_night')" v-text="form.errors.get('small_night')"></small>
        </td>
        <td>
            <input type="text" name="small_holiday" class="form-control" v-model="form.small_holiday">
            <small class="text-danger" v-if="form.errors.has('small_holiday')" v-text="form.errors.get('small_holiday')"></small>
        </td>

        <td>
            <input type="text" name="big_day" class="form-control" v-model="form.big_day">
            <big class="text-danger" v-if="form.errors.has('big_day')" v-text="form.errors.get('big_day')"></big>
        </td>
        <td>
            <input type="text" name="big_night" class="form-control" v-model="form.big_night">
            <big class="text-danger" v-if="form.errors.has('big_night')" v-text="form.errors.get('big_night')"></big>
        </td>
        <td>
            <input type="text" name="big_holiday" class="form-control" v-model="form.big_holiday">
            <big class="text-danger" v-if="form.errors.has('big_holiday')" v-text="form.errors.get('big_holiday')"></big>
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
        
        <td> {{  wage.name }}  </td>
        <td>
             {{  wage.small_day | formatMoney }} 
        </td>
        <td>
           {{  wage.small_night | formatMoney }} 
        </td>
        <td>
            {{  wage.small_holiday | formatMoney }} 
        </td>
        <td>
             {{  wage.big_day | formatMoney }} 
        </td>
        <td>
           {{  wage.big_night | formatMoney }} 
        </td>
        <td>
            {{  wage.big_holiday | formatMoney }} 
        </td>
        
        <td>
            <button v-if="canEdit"  @click.prevent="beginEdit" class="btn btn-primary btn-sm" >
                <i class="fa fa-edit"></i> 
            </button>
         
            
        </td>
       
    </tr>
</template>
<script>
    export default {
        name:'WageRow',
        props: {
            form: {
              type: Object,
              default: null
            },
            wage:{
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
                pointOptions:''
            }
        },
        computed:{
            canEdit(){
                if(!this.can_edit) return false;
                if(this.wage.code=='special') return false;
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
                this.$emit('edit',this.wage);
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
            clearErrorMsg(name) {
                this.form.errors.clear(name);
            },
                
        }
    }
</script>
