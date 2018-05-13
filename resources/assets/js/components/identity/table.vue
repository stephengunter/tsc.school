<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th v-if="false" style="width:15%">慈濟會員</th>
                        <th>名稱</th>
                        <th style="width:10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <create-row v-if="create" :form="createForm"
                        @cancel="cancelCreate" @submit="onSubmit" >
                        
                    </create-row> 
                    <row v-for="(identity,index) in model.viewList" :key="index" :index="index" :identity="identity"
                        :can_edit="can_edit" :form="form" :edit="form.id==identity.id"
                        @cancel="form.id=0" @submit="onSubmit" @edit="beginEdit" >
                        
                    </row>  
                    
                       
                </tbody>

            </table>
        </div>
        <slot name="table-footer"> 
        
        </slot> 
            
    </div>
</template>

<script>
import Row from './row.vue';
import CreateRow from './create.vue';
export default {
    name:'IdentityTable',
    components: {
        Row,
        'create-row':CreateRow
    },
    props: {
        model: {
            type: Object,
            default: null
        },
        create:{
            type: Boolean,
            default: false
        },
        can_edit:{
            type: Boolean,
            default: false
        }
	},
	data() {
		return {
			form:new Form({
                id:0,
                name:'',
                member:0,
            }),

            createForm:new Form({
                name:''
            }),
		};
	},
	computed:{
		
		
    }, 
	watch: {
		
	},
    methods:{
        beginEdit(identity) {

            this.form = new Form({
                id:identity.id,
                name:identity.name,

                member:identity.member
                
            });
        },
        onSubmit(){
            let save=null;
            if(this.create) save=Identity.store(this.createForm);
            else save=Identity.update(this.form.id,this.form);

            save.then(() => {
                    this.$emit('saved');
                    this.form.id=0;
                    Helper.BusEmitOK('資料已存檔');
                })
                .catch(error => {
                    
                    Helper.BusEmitError(error,'存檔失敗');
                })
        },
        onDelete(identity){
            this.$emit('remove' , identity);
        },
        cancelCreate(){
            this.$emit('cancel-create');
        }
        
   }
}
</script>

