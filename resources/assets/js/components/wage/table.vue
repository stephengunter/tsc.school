<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">名稱</th>
                        <th style="width:10%">小型課程-日間</th>
                        <th style="width:10%">小型課程-夜間</th>
                        <th style="width:10%">小型課程-假日</th>
                        <th style="width:10%">大型課程-日間</th>
                        <th style="width:10%">大型課程-夜間</th>
                        <th style="width:10%">大型課程-假日</th>
                        <th style="width:7%"></th>
                    </tr>
                </thead>
                <tbody>
                    <row v-for="(wage,index) in model.viewList" :key="index" :index="index" :wage="wage"
                        :can_edit="can_edit" :form="form" :edit="form.id==wage.id"
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
export default {
    name:'WageTable',
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
        beginEdit(wage) {

            this.form = new Form({
                id:wage.id,
                name:wage.name,

                small_day:Helper.formatMoney(wage.small_day),
                small_night:Helper.formatMoney(wage.small_night),
                small_holiday:Helper.formatMoney(wage.small_holiday),

                big_day:Helper.formatMoney(wage.big_day),
                big_night:Helper.formatMoney(wage.big_night),
                big_holiday:Helper.formatMoney(wage.big_holiday),
                
            });
        },
        onSubmit(){
            
            let save=Wage.update(this.form.id,this.form);

            save.then(() => {
                    this.$emit('saved');
                    this.form.id=0;
                    Helper.BusEmitOK('資料已存檔');
                })
                .catch(error => {
                    
                    Helper.BusEmitError(error,'存檔失敗');
                })
        },
        onDelete(wage){
            this.$emit('remove' , wage);
        }
        
   }
}
</script>

