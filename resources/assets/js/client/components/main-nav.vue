<template>
    <div v-if="hasItems" class="container">
        <nav class="tabs is-boxed">
            <ul>
                <li v-for="(item,index) in items" :key="index"  v-bind:class="{ 'is-active': isActive(item) }">
                    <a @click="setActive(item)" :href="getUrl(item)" style="font-size: 1.2em;">
                        {{  item.text }}
                    </a>
                </li>
                
            </ul>
        </nav>       
    </div>  
</template>

<script>
export default {
    name:'MainNav',
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
        if(!this.items) return;
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

