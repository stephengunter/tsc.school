<template>
   <div>
		<div class="row">
			<div class="col-sm-3">
				<h3>{{ title  }}</h3>
			</div>
			<div class="col-sm-3">
				
			</div>
			<div class="col-sm-3" style="margin-top: 20px;">
				
			</div>
			<div class="col-sm-3" style="margin-top: 20px;">

				<a @click.prevent="cancel" href="#" class="btn btn-default pull-right">
					<i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i>
					返回
				</a>
			</div>
		</div>
        <hr/>
        <edit :params="params" :terms="terms" :categories="edit_categories"
            @saved="onSaved"   @cancel="cancel" >                 
        </edit>
    </div>      
</template>

<script>
import CourseEdit from '../../components/course/edit';
export default {
	name:'CourseCreateView',
    components: {
        'edit':CourseEdit
    },
	props: {
		params: {
			type: Object,
			default: null
		},
		terms:{
			type:Array,
			default:null
		},
		categories:{
			type:Array,
			default:null
		},
            
    },
	data(){
		return {
			
            title:'新增課程',
			
		}
	},
	computed:{
		edit_categories(){
			if(!this.categories) return [];
			return this.categories.filter(item=>{
				return Helper.tryParseInt(item.value) > 0;
			});
        },
	},
	beforeMount() {
		
	}, 
	methods:{
		cancel(){
			this.$emit('cancel');
        },
        onSaved(course){
            this.$emit('saved',course);
        }
		
	}
}
</script>

