<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        
                        <th style="width:20%">名稱</th>
                        <th style="width:15%">所屬中心</th>
                        <th>群組中的教師</th>
                       
                        
                    </tr>
                </thead>
                <tbody v-if="hasData">
                    <tr v-for="(group,index) in getViewList()" :key="index">
                        
                        <td>
                            <a href="#" @click.prevent="onSelected(group.id)" v-text="group.name"> </a> 
                           
                        </td>
                        <td >{{  group.center.name }}</td>
                        <td >{{  group.teacherNames }}</td>
                        
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
		hasData(){
            let list=this.getViewList();
            if(!list) return false;
            return list.length > 0;
        }
        
		
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

