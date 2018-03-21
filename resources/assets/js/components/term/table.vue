<template>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:10%">年度</th>
                        <th style="width:10%">順序</th>
                        <th >名稱</th>
                        <th style="width:10%">報名起始日</th>
                        <th style="width:10%">早鳥截止日</th>
                        <th style="width:10%">開課決定日</th>
                        <th style="width:10%">狀態</th>
                        <th v-if="can_remove" style="width:10%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(term,index) in model.viewList" :key="index">
                        <td>{{  term.year }}</td>
                        <td>{{  term.order }}</td>
                        <td>
                             <a v-if="can_edit" href="#" @click.prevent="selected(term.id)" v-text="term.name"> </a> 
                             <span v-else v-text="term.name"></span>
                        </td>
                        <td> {{ term.openDate }}  </td>
                        <td> {{ term.birdDate }}  </td>
                        <td> {{ term.closeDate }}  </td>   
                        <td v-html="$options.filters.activeLabel(term.active)" ></td>
                        
                        <td v-if="can_remove" >
                           
                            <button class="btn btn-xs btn-danger"  @click.prevent="remove(term)">
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
    name:'TermTable',
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
        
        selected(id){
           this.$emit('selected',id);
        },
        remove(term){
            this.$emit('remove' , term);
        },
        
   }
}
</script>

