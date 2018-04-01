<template>
    <div class="container">
        <nav class="nav">
            <div class="nav-left">
                <a href="/"  @click="setCurrent('/')"  class="nav-item">
                    <img src="../../../../assets/images/logo.gif" alt="">
                </a>
            </div>
            <div v-if="model" style="font-size:1.2em" class="nav-center is-hidden-mobile">
                
                <a href="/centers" @click="setCurrent('centers')"  :class="getMenuStyle('centers')">
                    <i aria-hidden="true" class="fa fa-university fa-fw"></i>&nbsp; 開課中心
                </a>
                <a href="/courses" :class="getMenuStyle('courses')">
                    <i aria-hidden="true" class="fa fa-book fa-fw"></i>&nbsp; 課程總覽
                </a>
                <a href="/about" :class="getMenuStyle('about')">
                    <i aria-hidden="true" class="fa fa-info-circle fa-fw"></i>&nbsp; 關於我們
                </a>
                <a href="/docs"  :class="getMenuStyle('docs')">
                    <i aria-hidden="true" class="fa fa-download fa-fw"></i>&nbsp; 文件下載
                </a>
                <a href="/faq"  :class="getMenuStyle('faq')">
                    <i aria-hidden="true" class="fa fa-question-circle fa-fw"></i>&nbsp; 常見問題
                </a>
            </div>
            <span id="nav-toggle" style="display:none" class="nav-toggle">
                <span></span> <span></span> <span></span>
            </span> 
            <div id="nav-menu" class="nav-right nav-menu nav-item">
            </div>
        </nav>
    </div>  
</template>

<script>
export default {
    name:'TopNav',
    props: {
        model: {
            type: Object,
            default: null
        },
	},
    data() {
        return {
            current:''
        }
    },
    methods: {
        setMenus(model){
           
            this.setCurrent(model.current);

        },
        setCurrent(val){
            this.current=val;
        },
        getMenuStyle(key){
            if(!this.current)  this.current=this.model.current;
            
            if(this.current.startsWith(key))  return "nav-item is-tab is-active";
		    return "nav-item is-tab";
        },
        
        

    },
}
</script>

