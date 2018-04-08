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
                <a v-if="false" href="/docs"  :class="getMenuStyle('docs')">
                    <i aria-hidden="true" class="fa fa-download fa-fw"></i>&nbsp; 文件下載
                </a>
                <a href="/faq"  :class="getMenuStyle('faq')">
                    <i aria-hidden="true" class="fa fa-question-circle fa-fw"></i>&nbsp; 常見問題
                </a>
            </div>
            <span id="nav-toggle" class="nav-toggle">
                <span></span> 
                <span></span>
                <span></span>
            </span> 
            <div  v-if="isAuth" id="nav-menu" class="nav-right nav-menu nav-item"  >
                <user-menus :user="model.user"></user-menus>
            </div>
            <div v-else  id="nav-menu"  class="nav-right nav-menu" style="font-size:1.2em" >
                <a href="/login"  :class="getMenuStyle('login')">
                    <i aria-hidden="true" class="fa fa-sign-in fa-fw"></i>&nbsp; 登入
                </a>
            </div>
            
        </nav>
    </div>  
</template>

<script>
import UserMenus from './user-menus.vue';
export default {
    name:'TopNav',
    components:{
        'user-menus':UserMenus,
    },
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
    computed:{
        isAuth(){
            if(!this.model) return false;
            if(!this.model.user) return false;
            if(!this.model.user.id) return false;
            return true;

        }    
            
    },
    methods: {
        onLogout(){
            let form=new Form();
            let url='/logout';
            form.post(url)
            .then(() => {
                location.href='/';
            })
        },
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

<style scoped>
.menu-list a {
    padding: 5px 16px;
    line-height: 25px;
    border-radius: 0;
    color: #000;
    opacity: .75;
}
.menu-list a:hover {
    background-color: whitesmoke;
    color: #00d1b2;
}
</style>


