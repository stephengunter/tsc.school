<template>
    <div v-if="hasAreas">
        <div v-for="(area,index) in areas" :key="index" :class="{ 'paddingTop': index > 0 }">
            <h1 class="title">{{ area.name }}</h1>
            <div class="columns is-multiline">
                <div v-for="(center,index) in area.centers" :key="index"  class="column is-one-quater-mobile is-half-tablet is-half-desktop">
                    <center-card :center="center" @selected="onSelected"></center-card>
                </div>
            </div>
        </div>

        <h1 v-show="hasOverseas" class="title paddingTop">海外</h1>
        <div class="columns is-multiline">
            <div v-for="(center,index) in overseas" :key="index"  class="column is-one-quater-mobile is-half-tablet is-half-desktop">
                <center-card :center="center"></center-card>
            </div>
        </div>

    </div>
    
</template>

<script>
import Card from '../../components/center/card.vue'
export default {
    name:'CenterIndexView',
    components:{
       'center-card':Card
    },
    props: {
        areas: {
            type: Array,
            default: null
        },
        overseas: {
            type: Array,
            default: null
        },
    },
    data(){
        return {
           
        }
    },
    computed: {
        hasAreas(){
            if(!this.areas) return false;
            return this.areas.length > 0;
        },
        hasOverseas(){
            if(!this.overseas) return false;
            return this.overseas.length > 0;
        }
    },
    beforeMount(){
       
    },
    mounted(){
        this.$emit('loaded');
    },
    methods:{
        onSelected(id){
            alert(id);
        }
      
    }
}
</script>




<style scoped>
    
    .paddingTop {
        padding-top: 1em;
    }
</style>

