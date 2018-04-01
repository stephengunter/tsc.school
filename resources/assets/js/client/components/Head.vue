<template>
    <div>
        <div class="container">
            <nav class="nav">
                <div class="nav-left">
                    <a href="/" class="nav-item">

                        <img src="../../../../assets/images/logo.gif" alt="">
                    </a>
                </div>
                <div v-if="model" style="font-size:1.2em" class="nav-center is-hidden-mobile">
                    
                    <a href="/centers" @click="setCurrent('centers')"  :class="GetMenuStyle('centers')">
                        <i aria-hidden="true" class="fa fa-university fa-fw"></i>&nbsp; 開課中心
                    </a>
                    <a href="/courses" :class="GetMenuStyle('courses')">
                        <i aria-hidden="true" class="fa fa-book fa-fw"></i>&nbsp; 課程總覽
                    </a>
                    <a href="/about" :class="GetMenuStyle('about')">
                        <i aria-hidden="true" class="fa fa-info-circle fa-fw"></i>&nbsp; 關於我們
                    </a>
                    <a href="/docs"  :class="GetMenuStyle('docs')">
                        <i aria-hidden="true" class="fa fa-download fa-fw"></i>&nbsp; 文件下載
                    </a>
                    <a href="/faq"  :class="GetMenuStyle('faq')">
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
        
    </div>  
</template>

<script>
export default {
    name:'Head',
    props: {
        title: {
            type: String,
            default: ''
        },
	},
    data() {
        return {
            
            model:null,
            current:''
        }
    },
    created() {
        Bus.$on('menus',this.setMenus);
        Bus.$on('errors',this.showErrorMsg);
        Bus.$on('okmsg',this.showSuccessMsg);
    },
    methods: {
        setMenus(model){
            this.model={
                ...model
            };

            this.setCurrent(model.current);

        },
        setCurrent(val){
            this.current=val;
        },
        GetMenuStyle(key){
            if(this.current.startsWith(key))  return "nav-item is-tab is-active";
		    return "nav-item is-tab";
        },
        showErrorMsg(msg) {
            if(!msg) msg='系統暫時無回應,請稍候再試.';
            this.$notify.open({
                    content: msg,
                    type: 'danger',
                    placement: 'top-center',
                    duration: 1500,
                }) 
        },
        showSuccessMsg(msg) {
            if(!msg) msg='存檔成功.';
            this.$notify.open({
                    content: msg,
                    type: 'success',
                    placement: 'top-center',
                    duration: 1500,
                }) 
        },
        
        

    },
}
</script>

