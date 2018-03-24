<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:15%">名稱</th>
                        <th style="width:5%">代碼</th>
                        <th>地址</th>
                        <th style="width:10%">電話</th>
                        <th style="width:10%">傳真</th>
                        <th style="width:15%">課程洽詢電話</th>
                        <th style="width:10%">狀態</th>
                        <th v-if="can_order" style="width:10%">順序 
                            <button class="btn btn-success btn-xs" @click.prevent="saveOrders">
                                <i class="fa fa-save"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(center,index) in model.viewList" :key="index">
                        <td>
                            <a href="#" @click.prevent="onSelected(center.id)" v-text="center.name"> </a> 
                           
                        </td>
                        <td>{{  center.code }}</td>
                        <td>
                            <span v-if="hasAddress(center)">
                                {{  center.contactInfo.address.fullText }}
                            </span>
                        </td>
                        <td>
                            <span v-if="hasContactInfo(center)">
                                 {{  center.contactInfo.tel }}
                            </span>
                           
                        </td>
                        <td>
                            <span v-if="hasContactInfo(center)">
                                 {{  center.contactInfo.fax }}
                            </span>
                            
                        </td>
                        <td>{{  center.courseTel }}</td>
                        <td v-html="$options.filters.activeLabel(center.active)" ></td>
                        <td>
                            <button class="btn btn-sm btn-default" v-if="can_order" @click.prevent="up(center,index)">
                                <i class="fa fa-arrow-up" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-sm btn-default" v-if="can_order"  @click.prevent="down(center,index)">
                                <i class="fa fa-arrow-down" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>    
                </tbody>

            </table>
        </div>
        <slot name="table-footer"> 
        
        </slot> 
            
    </div>
</template>

<script>
export default {
    name:'CenterTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        can_order:{
            type: Boolean,
            default: false
        }
        
	},
	data() {
		return {
			
		};
	},
	computed:{
		
		
    }, 
	watch: {
		
	},
    methods:{
        hasContactInfo(center){
            if(center.contactInfo) return true;
            return false;
        },
        hasAddress(center){
             
            if(!this.hasContactInfo(center)) return false;
            if(center.contactInfo.address) return true;
            return false;
        },
        onSelected(id){
           this.$emit('selected',id);
        },
        up(center,index){
				
            this.$emit('up' , center, index);

        },
        down(center,index){
            this.$emit('down' , center, index);
        },
        saveOrders(){
            this.$emit('save-orders');
        }
        
   }
}
</script>

