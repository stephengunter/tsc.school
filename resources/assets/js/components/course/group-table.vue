<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        
                        <th style="width:45%">名稱</th>
                        <th style="width:45%">所屬中心</th>
                        <th style="width:10%">狀態</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(group,index) in model.viewList" :key="index">
                        
                        <td>
                            <a href="#" @click.prevent="onSelected(group.id)" v-text="group.name"> </a> 
                           
                        </td>
                        <td >{{  group.centerName }}</td>
                        
                        <td v-html="$options.filters.activeLabel(group.active)" ></td>
                        
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
    name:'TeacherGroupTable',
    props: {
        model: {
            type: Object,
            default: null
        },
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
        getViewList(){
			if(this.model) return this.model.viewList;
			return null;
		},
        onSelected(id){
           
           this.$emit('selected',id);
        },
        
   }
}
</script>

