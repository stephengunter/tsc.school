<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:15%">名稱</th>
                        <th style="width:5%">需要證明</th>
                        <th style="width:10%">第一階段折扣</th>
                        <th style="width:10%">第二階段折扣</th>
                        <th style="width:10%">備註</th>
                        <th style="width:7%"></th>
                    </tr>
                </thead>
                <tbody>
                    <row v-for="(discount,index) in model.viewList" :key="index" :index="index" :discount="discount"
                        :point_options="pointOptions" :can_edit="can_edit" :form="form" :edit="form.id==discount.id"
                        @cancel="form.id=0" @submit="onSubmit" @edit="beginEdit" 
                        @delete="onDelete">
                        
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
export default {
    name:'DiscountTable',
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
       
        
	},
	data() {
		return {
			form:new Form({
                id:0,
                name:'',
                prove:0,
                pointOne:100,
                pointTwo:100,
            }),

            pointOptions:Helper.numberOptions(50, 100, false, 5)
		};
	},
	computed:{
		
		
    }, 
	watch: {
		
	},
    methods:{
        beginEdit(discount) {

            this.form = new Form({
                id:discount.id,
                name:discount.name,
                prove:discount.prove,
                pointOne:discount.pointOne,
                pointTwo:discount.pointTwo
            });
        },
        onSubmit(){
            let save=Discount.update(this.form.id,this.form);

            save.then(() => {
                    this.$emit('saved');
                    this.form.id=0;
                    Helper.BusEmitOK('資料已存檔');
                })
                .catch(error => {
                    
                    Helper.BusEmitError(error,'存檔失敗');
                })
        },
        onDelete(discount){
            this.$emit('remove' , discount);
        }
        
   }
}
</script>

