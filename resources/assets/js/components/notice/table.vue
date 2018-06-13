<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">公告中心</th>
                        <th style="width:5%">置頂</th>
                        <th style="width:10%">日期</th>
                        <th>標題</th>
                        <th style="width:7%">狀態</th>
                        <th style="width:7%">審核</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(notice,index) in model.viewList" :key="index">
                        <td>{{  notice.center.name }}</td>
                        <td>
                             <i v-if="isTrue(notice.top)" class="fa fa-check-circle" style="color:green"></i>
                        </td>
                        <td>{{  notice.date }}</td>
                        <td>
                             <a href="#" @click.prevent="selected(notice.id)" v-text="notice.title"> </a> 
                             
                        </td>
                        <td v-html="activeLabel(notice)" ></td>
                        <td v-html="$options.filters.reviewedLabel(notice.reviewed)" ></td>
                     
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
    name:'NoticeTable',
    props: {
        model: {
            type: Object,
            default: null
        },
        can_edit:{
            type: Boolean,
            default: false
        },
        can_remove:{
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
        activeLabel(notice){
            return Notice.activeLabel(notice.active);
        },
        isTrue(val){
            return Helper.isTrue(val);
        },
        selected(id){
           this.$emit('selected',id);
        },
        remove(notice){
            this.$emit('remove' , notice);
        },
        
   }
}
</script>

