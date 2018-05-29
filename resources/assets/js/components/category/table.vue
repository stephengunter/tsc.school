<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:12%"></th>
                        <th style="width:10%">代碼</th>
                        <th>名稱</th>
                        <th style="width:10%">類型</th>
                        <th v-if="false" style="width:12%">順序 
                            <button class="btn btn-success btn-xs" @click.prevent="saveOrders">
                                <span aria-hidden="true" class="glyphicon glyphicon-floppy-disk"></span>
                            </button>
                        </th>
                        <th style="width:7%"></th>
                    </tr>
                </thead>
                <tbody>
                    <row  v-if="creating" :form="form"   :edit="true" 
                       @cancel="creating=false" @submit="onSubmit">
                    </row>

                    <row v-for="(category,index) in model.viewList" :key="index" :index="index" :category="category"
                     :can_edit="can_edit" :form="form" :edit="form.id==category.id"
                     @cancel="form.id=0" @submit="onSubmit" @edit="beginEdit" 
                     @up="up(category,index)"  @down="down(category,index)" @delete="onDelete">
                        
                    </row>  
                </tbody>

            </table>
        </div>
        <slot name="table-footer"> 
        
        </slot> 
            
    </div>
</template>

<script>
import Row from './row.vue'
export default {
    name:'CategoryTable',
    components: {
        Row
    },
    props: {
        model: {
            type: Object,
            default: null
        },
        can_edit:{
            type: Boolean,
            default: false
        },
        can_order:{
            type: Boolean,
            default: false
        },
        
	},
	data() {
		return {
			creating:false,
            form:new Form({
                id:0,
                name:'',
                code:'',
                importance:0,
                active:true
            }),
		};
	},
	computed:{
		
		
    }, 
	watch: {
	
	},
    methods:{
        init(){
            this.creating=false;
            this.form.id=0;
        },
        onCreate(){
            
            this.form = new Form({
                id:0,
                name:'',
                code:'',
                importance:0,
                active:true
            });
            this.creating=true;
        },
        onSelected(id){
           this.$emit('selected',id);
        },
        up(category,index){
				
            this.$emit('up' , category, index);

        },
        down(category,index){
            this.$emit('down' , category, index);
        },
        saveOrders(){
            this.$emit('save-orders');
        },
        beginEdit(category) {

            this.form = new Form({
                id:category.id,
                name:category.name,
                code:category.code,
                importance:category.importance,
                active:category.active
            });
        },
        onSubmit(){
            let save=null;
            if(this.form.id < 1) save=Category.store(this.form);
            else save=Category.update(this.form.id,this.form);

            save.then(() => {
                    this.$emit('saved');
                    this.init();
                    Helper.BusEmitOK('資料已存檔');
                })
                .catch(error => {
                    
                    Helper.BusEmitError(error,'存檔失敗');
                })
        },
        onDelete(category){
            this.$emit('delete',category);
        }
        
   }
}
</script>

