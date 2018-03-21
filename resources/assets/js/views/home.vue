<template>
<div v-if="loaded">
    <div class="row">
        <div v-if="systemsCount > 0" class="col-md-6">
            <menu-item  :title="getKey(0)" :items="getItems(0)" :badges="badges"></menu-item>
        </div>
        <div v-if="systemsCount > 1" class="col-md-6">
            <menu-item  :title="getKey(1)" :items="getItems(1)" :badges="badges"></menu-item>
        </div>
    </div> 
    <div class="row">
        <div v-if="systemsCount > 2" class="col-md-6">
            <menu-item  :title="getKey(2)" :items="getItems(2)" :badges="badges"></menu-item>
        </div>
        <div v-if="systemsCount > 3" class="col-md-6">
            <menu-item  :title="getKey(3)" :items="getItems(3)" :badges="badges"></menu-item>
        </div>
    </div> 
 </div>   
</template>

<script>
    import MenuItem from '../components/menu.vue'
    

    export default {
        name: 'HomeView',
        components: {
            'menu-item':MenuItem,
        },
        props: {
			systems:{
				type:Array,
				default:null
            },
            badges:{
				type:Array,
				default:null
			},
        },
        data() {
            return {
            

            }
        },
        computed:{
            loaded(){
                
                if(!this.systems) return false;
                return this.systemsCount > 0;

            },
            systemsCount(){
                return this.systems.length;
            }
        }, 
        beforeMount(){
            this.init();
        },
        methods: {
            init() {
               
              
            },
            getKey(index){
                return this.systems[index].key;
            },
            getItems(index){
                return this.systems[index].menus;
            }
            
            
        }
    }
</script>
