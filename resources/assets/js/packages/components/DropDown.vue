<template>
   <div v-if="selectedItem" class="dropdown">
		<slot  name="label"> 
     
		</slot> 
			
		<button type="button" :class="btnStyle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			{{ selectedItem.text }}
		</button>
		<div class="dropdown-menu">
			<a v-for="(item,index) in otherItems" :key="index" class="dropdown-item" 
			href="#" @click.prevent="onSelectedChanged(item)">
				{{ item.text }}
			</a>
			
		</div>
   </div>
</template>


<script>
export default {
   name:'DropDown',
   props: ['items','selected','btn_style'],
	beforeMount() {
		this.init();
	},
	data(){
		return {
			selectedItem:null,
			otherItems:[],
			btnStyle:''
		}
	},
	watch: {
		'selected':'init',
		'items':'init'
	},
    methods:{
		init(){
			this.selectedItem=this.items.find((item)=>{
				return item.value==this.selected;
			});
			this.otherItems=this.items.filter((item)=>{
				return item.value!=this.selected;
			});

			if(this.btn_style) this.btnStyle= `btn btn-${this.btn_style} dropdown-toggle`;
			else this.btnStyle= 'btn btn-default dropdown-toggle';
		},
		onSelectedChanged(item){
			this.$emit('selected',item);
		},
      
    }
}
</script>


