<template>
    <div v-if="hasItems" class="container">
        <nav class="tabs is-boxed">
            
            <ul>
                <li v-for="(item,index) in items" :key="menuIndexes[index]" v-bind:class="{ 'is-active': isActive(item) }">
                    <a @click="setActive(item)" :href="item.url" style="font-size: 1.2em;">
                        {{  item.text }}
                    </a>
                </li>
                <li  v-for="(area,areaIndex) in areas" :key="areaIndexes[areaIndex]">
                    <dropdown trigger="hover"> 
                        <a class="button is-outlined is-primary" style="font-size: 1.2em;">
                            {{ area.name }}
                            <span class="icon is-small">
                                <i class="fa fa-angle-down"></i>
                            </span>
                        </a>
                        <div  slot="content" class="menu-list">
                        
                            <a style="font-size: 1.2em;" v-for="(center,centerIndex) in area.centers" :key="centerIndex" :href="center.url" >  
                            
                                {{ center.name }}
                            </a>
                            
                        </div>                  
                    </dropdown>
                </li>
                
            </ul>

            


        </nav>   
           
    </div>  
    
</template>

<script>
export default {
    name:'MainNav',
    props: {
        areas: {
            type: Array,
            default: null
        },
        items: {
            type: Array,
            default: null
        },
	},
    data() {
        return {
            menuIndexes:[],
            areaIndexes:[],
            selected:0
        }
    },
    beforeMount() {
        if(!this.hasItems) return;
        var active = this.items.find((item) => {
            return Helper.isTrue(item.active);
        });

        this.selected = active.value;

        for(let i=0; i<20; i++){
            this.menuIndexes.push(i);
        }

        for(let y=30; y<50; y++){
            this.areaIndexes.push(y);
        }
           
    },
    computed:{
        hasItems(){
            if(!this.items) return false;
            return this.items.length > 0;
        },
        total(){
            if(this.hasItems){
                if(this.areas) return this.items.length + this.areas.length;
                return this.items.length;
            }else{
                return 0;
            }
        },
	},
    methods: {
        isActive(item){
            return item.value == this.selected;
        },
        setActive(item){
            this.selected = item.value;
        }
        
        

    },
}
</script>

