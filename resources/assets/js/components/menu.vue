<template>
    <div class="panel panel-default show-data">
        <div class="panel-heading">
            <span class="panel-title">
                <h4 v-html="getTitle()">
                </h4>
            </span> 
        </div>  <!-- End panel-heading-->
        <div class="panel-body" >
            <div class="row">
                <div v-show="hideItem(item)" v-for="(item,index) in items" :key="index" class="col-md-6">
                    <ul style="list-style-type: square;">
                        <li> 
                            <a :href="item.path"> {{ item.text  }} </a>
                            <span v-if="hasUnReviewTeachers(item.path)" class="badge badge-pill  badge-primary">
                               {{ badges.unreviewTeachers  }}
                            </span>
                           
                        </li> 
                        
                    </ul>                 
                </div>
               
            </div>   <!-- End row-->
     
        </div><!-- End panel-body-->
    </div>
</template>

<script>
    export default {
        name: 'MenuItem',       
        props: {
            title: {
                type: String,
                default: ''
            },
            items:{
                type: Array,
                default: []
            },
            badges: {
                type: Object,
                default: null
            },
            
        },
        data() {
            return {
                titleHtml:'',
                unReviewTeachers:0
            }
        },
        beforeMount(){
           
        },
        methods: {
            getTitle(title) {
                return Menus.getTitleHtml(this.title);
               
            },
            hideItem(item){
                if(item.hide) return false;
                return true;
            },
            hasUnReviewTeachers(path){
                if(path!='/teachers/review') return false;
                if(!this.badges)  return false;
                return this.badges.unreviewTeachers > 0;
            }
        },

    }
</script>