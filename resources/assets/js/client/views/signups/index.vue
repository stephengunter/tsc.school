<template>
    <div>
        <div class="columns">
            <div class="column">
                <h2 v-if="noData" class="title">
                    目前沒有您的報名紀錄.
                </h2>
                <h2 v-else>
                    您的報名紀錄
                </h2>
            </div>
            
        </div>
        <div v-if="!noData" class="columns" style="padding-top:1em">
            <signup-table :signups="signups" @remove="onRemove"></signup-table>
        </div>
    </div>
        
    
</template>


<script>
    import SignupTable from '../../components/signup/table.vue';
    export default{
        name:'SignupIndexView',
        components: {
            'signup-table':SignupTable        
        },
        props: {
            model: {
                type: Array,
                default: null
            }
        },
        data(){
            return {
                signups:[],
                deleteId:0
            }
        },
        computed:{
            noData(){
                return this.signups.length < 1 ;
            }
            
           
        },
        beforeMount(){
            this.signups=this.model.slice(0);
        },
        methods: {
            init(){
               
               
            },
            fetchData(){
                let getData = Signup.index();

                getData.then(signups => {

                    this.signups=signups.slice(0);

                })
                .catch(error => {
                    Bus.$emit('errors');
                
                })
            },
            onRemove(signup){
                this.deleteId=signup.id;
                this.$modal.confirm({
                    content: '確定要刪除這筆報名紀錄嗎?',
                    title:'',
                    okText:'確定',
                    cancelText:'取消',
                    onOk: this.deleteSignup,
                });
            },
            deleteSignup(){
                let remove=Signup.remove(this.deleteId);
                remove.then(() => {
                    
                    this.fetchData();
                     
                }).catch(error => {
                    Bus.$emit('errors','刪除失敗');
                })
            }
            
            
            
        },



    }
</script>

