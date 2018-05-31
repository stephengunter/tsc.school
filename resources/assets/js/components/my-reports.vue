<template>
   
</template>

<script>
    export default {
        name: 'MyReports',       
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