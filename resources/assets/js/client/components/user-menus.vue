<template>
    <dropdown ref="userdropdown" >     
        <a class="button is-outlined is-primary">
            <i class="fa fa-user-circle-o" aria-hidden="true"></i>
            &nbsp;{{ user.name }}
            <span class="icon is-small">
                <i class="fa fa-angle-down"></i>
            </span>
        </a>
        <div slot="content" class="menu-list">
           
            <a v-if="false" href="#" @click.prevent="onLogout">  
                <i class="fa fa-sign-out fa-fw" aria-hidden="true"></i>
                登出
            </a>
        
        
            <hr v-if="false"  class="dropdown-divider" style="margin: 5px 0;">

            <a href="#" @click.prevent="onLogout">  
                <i class="fa fa-sign-out fa-fw" aria-hidden="true"></i>
                登出
            </a>
            <a href="/user/password/change" >  
                <i class="fa fa-key fa-fw" aria-hidden="true"></i>
                變更密碼
            </a>
            <a href="/user/password/change" >  
                <i class="fa fa-id-card fa-fw" aria-hidden="true"></i>
                聯絡資訊
            </a>
        </div>                  
    </dropdown>
    
</template>

<script>
export default {
    name:'UserMenus',
    props: {
        user: {
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
        
            
    },
    methods: {
        onLogout(){
            let form=new Form();
            let url='/logout';
            form.post(url)
            .then(() => {
                location.href='/';
            })
        }

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


