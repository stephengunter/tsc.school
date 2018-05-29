<template>
    <nav v-show="total" class="pagination is-right">
        <a @click="interCurrent-=1" class="pagination-previous" :disabled="interCurrent<=1"  >上一頁</a>
        <a @click="interCurrent+=1" class="pagination-next"  :disabled="interCurrent>=maxPage">下一頁</a>
        <ul class="pagination-list">
            <li v-if="hasLess" @click="interCurrent=1" ><a class="pagination-link">1</a></li>
            <li v-if="hasLess">
               
                <a v-if="showLess" @click="onShowLess" @mouseleave="showLess=!showLess" class="button is-primary is-inverted"  style="font-size: 0.9rem;">
                    <i class="fa fa-angle-double-left" ></i>
                </a>
                <a v-else @mouseover="showLess=!showLess" ><span class="pagination-ellipsis">&hellip;&hellip;</span></a>
            </li>
            <li v-for="(item,index) in items" :key="index" @click="interCurrent=item.page" ><a :class="item.class">{{ item.page }}</a></li>          
            <li v-if="hasMore">
               
                <a v-if="showMore" @click="onShowMore" @mouseleave="showMore=!showMore" class="button is-primary is-inverted"  style="font-size: 0.9rem;">
                    <i class="fa fa-angle-double-right" ></i>
                </a>
                <a v-else @mouseover="showMore=!showMore" ><span class="pagination-ellipsis">&hellip;&hellip;</span></a>
            </li>
            <li v-if="hasMore"><a class="pagination-link" @click="interCurrent=maxPage" >{{ maxPage }}</a></li>
        </ul>
    </nav>
</template>


<script>
    export default {
        name:'Pager',
        props: {
            model:{
               type: Object,
               default: null,
            },
        },
        beforeMount(){
           
        },
        data(){
            return{
                interCurrent: 1,
                interPageSize: 10,
              

                items:[],
                hasMore:false,
                hasLess:false,

                showMore:false,
                showLess:false,
            }
        },
        computed: {
            page_size(){
                if(!this.model) return 10;
                return this.model.pageSize;
            },
            current(){
                if(!this.model) return 1;
                return this.model.pageNumber;
            },
            total(){
                if(!this.model) return 0;
                return this.model.totalItems;
            },
            showFirstPage(){
                return false
            },
            hasNextPage() {
                if(!this.model) return false;
                return this.model.hasNextPage
            },
            maxPage(){
                 return this.model.maxPage;
            }
        },
        watch: {
            current(val) {
               if (val !== this.interCurrent) {
                  this.interCurrent=val
               }
            },
            interCurrent(val){
                this.$emit('page-change',val)
                this.onCurrentPageChanged()
            },
            page_size(val) {
               if (val !== this.interPageSize) {
                  this.interPageSize=val
               }
            },
            total(){
               
                 this.createItems()
            }
        },
        mounted() {
            this.interPageSize=this.page_size;
            this.interCurrent=this.current;
            this.createItems();
        },
        methods:{
            calcTotalPage() {
               return Math.floor((this.total - 1) / this.interPageSize) + 1;
            },
            onCurrentPageChanged() {

                this.createItems()
                  
            },
            createItems(){
              
                let curren=this.interCurrent
                let min=curren-2
                if(min < 1) min=1
                let max=min+4
                if(max > this.maxPage) max=this.maxPage
                this.items=[]
                alert(min);
                 alert(max);
                for(let i=min; i<=max; i++){
                   
                    let className='pagination-link'
                    if(i==curren) className+= ' is-current'
                    let item={
                        class:className,
                        page:i
                    }
                    this.items.push(item)
                   
                }

                this.hasMore=max<this.maxPage
                this.hasLess=min>1

                
            },
            onShowMore(){
                 let curren=this.interCurrent
                 curren += 5
                 if(curren>this.maxPage) curren=this.maxPage
                 this.interCurrent=curren
            },
            onShowLess(){
                 let curren=this.interCurrent
                 curren -= 5
                 if(curren<1) curren=1
                 this.interCurrent=curren
            }
            
        },
        
    }

</script>

<style>
   .pagination.is-medium {
    font-size: 1.25rem;
}
</style>