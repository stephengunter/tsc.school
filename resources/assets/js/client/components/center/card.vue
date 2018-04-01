<template>
    <div class="card"> 
        <div class="card-content">
            <div class="center.photo">
                <div v-if="false" class="media-left is-hidden-mobile">
                    <figure  class="image is-128x128">
                        <a @click.prevent="onSelected"> 
                           <img :src="photo.path">
                        </a>
                    </figure>
                </div>
                
                <div class="media-content">
                    <div>
                        <ul class="info">   
                            <li class="title-item">
                                
                                <span v-if="isTrue(center.oversea)" v-text="center.name"></span>
                                <a v-else @click.prevent="onSelected">  
                                    {{ center.name}}
                                </a>
                            </li>                      
                            <li v-if="hasAddress(center)"  class="item">
                                <i class="fa fa-map-marker fa-fw" aria-hidden="true"> </i>
                               
                                 {{  center.contactInfo.address.fullText }}
                            </li>
                            <li v-if="hasTel(center)" class="item">
                                <i class="fa fa-volume-control-phone fa-fw" aria-hidden="true"> </i>
                                 {{  center.contactInfo.tel }}
                          
                            </li>
                        </ul>   

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name:'CenterCard',
    props:{
        center :{
            type:Object,
            default:null
        }, 
    },
    data(){
        return {
            
        }
    },
    computed: {
        
    },
    beforeMount() {
        this.init()

    },
    methods:{
        init(){
           // this.getPhoto()                
        },       
        isTrue(val){
            return Helper.isTrue(val);
        },   
        hasTel(center){
            if(!center.contactInfo) return false;
            if(center.contactInfo.tel) return true;
            return false;
        },
        hasAddress(center){
             
            if(!center.contactInfo) return false;
            if(center.contactInfo.address) return true;
            return false;
        },
        onSelected(){
            this.$emit( 'selected' , this.center.id);
        }
        
    } 
}
</script>

<style scoped>
    .card {
        width:100%
    }
    ul.info {
        list-style-type:none;        
    }
    li.title-item {
        font-size: 1.75em;
        font-weight: normal;
        margin-bottom: 0.5714em;
    }
    li.item {
        font-size: 1.125em;
        margin-bottom: 0.8888em;
    }
</style>

