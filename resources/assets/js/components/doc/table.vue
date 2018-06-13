<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>名稱</th>
                        <th v-if="can_edit" style="width:10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(doc,index) in model.viewList" :key="index">
                        <td>
                            <a :href="doc.url"  v-text="doc.title"> </a> 
                           
                        </td>
                        
                        <td v-if="can_edit">
                            <button   @click.prevent="selected(doc.id)" class="btn btn-primary btn-sm" >
                                <i class="fa fa-edit"></i> 
                            </button>
                            &nbsp;
                            <button   @click.prevent="remove(doc)" class="btn btn-danger btn-sm" >
                                <i class="fa fa-trash"></i> 
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
    name:'DocTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        can_edit:{
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
        selected(id){
           this.$emit('selected',id);
        },
        remove(id){
           this.$emit('remove',id);
        },
        
   }
}
</script>

