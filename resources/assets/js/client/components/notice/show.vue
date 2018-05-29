<template>
    <div>
        <h1 v-if="show_title" class="title" v-text="title"></h1>
        
        <h3 class="title is-3" style="padding-top:1em;color:#003c99"> {{ notice.title }}</h3>
        <p class="subtitle is-5">{{ notice.date }}</p>
        <hr>
        <div class="content" style="font-size:1.2em;" >
            <p v-html="notice.content"></p>
        </div>
        <div style="clear: both;text-align:right;">
             <a @click.prevent="onBack" class="button is-primary is-outlined">
                <span class="icon is-small">
                 <i class="fa fa-angle-double-left"></i>
                </span>
                <span>返回</span>
             </a>
           
        </div>
    
    </div>
</template>

<script>
  
    export default {
        name:'ShowNotice',
        props: {
            notice: {
              type: Object,
              default: null
            },
            show_title:{
              type: Boolean,
              default: true
            },
        },
        beforeMount(){
            
        },
        data(){
            return{
                title:'公告訊息',
              
                back:'<<返回'
                
            }
        },
        methods:{
            fetchData(){
                if(!this.id) return
                let getData=Notice.show(this.id)
                getData.then(data => {
                    this.notice=data.notice  
                    this.title='公告訊息：' + this.notice.title
                    this.$emit('loaded',this.notice)               
                })
                .catch(error=> {
                    Bus.$emit('errors')                      
                })

            },
            
            onBack(){
                this.$emit('back')    
            }
        },
        
    }
</script>