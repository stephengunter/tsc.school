<template>
   
    <nav v-if="hasItems" class="nav has-shadow is-hidden-mobile" >
        <div class="container">
            <div class="nav-left">
                <a v-for="(item,index) in items" :key="index"  :href="item.url" :class="{ 'nav-item is-tab':true ,  'is-active': isActive(item)}" style="font-size: 1.2em;">
                     {{  item.text }}
                </a>
               
            </div>
        </div>
        
    </nav>       
  
    
</template>

<script>
export default {
    name:'SubNav',
    props: {
        items: {
            type: Array,
            default: null
        },
	},
    data() {
        return {
            selected:0
        }
    },
    beforeMount() {
        if(!this.hasItems) return;
        var active = this.items.find((item) => {
            return Helper.isTrue(item.active);
        });

        this.selected = active.value;
    },
    computed:{
        hasItems(){
            if(!this.items) return false;
            return this.items.length > 0;
        }
	},
    methods: {
        isActive(item){
            return item.value == this.selected;
        },
        setActive(item){
            this.selected = item.value;
        },
        getUrl(item){
            return `about/?id=${item.value}`;
        },
        
        

    },
}
</script>

