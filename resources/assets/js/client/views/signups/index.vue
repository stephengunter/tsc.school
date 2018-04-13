<template>
    <div>
        <div class="columns">
            <div v-if="noData"  class="column">
                
                <h2 v-if="noData" class="title">
                    目前沒有您的報名紀錄.
                </h2>
               
            </div>
            <div v-else  class="column">
                
                <h2 v-if="unpays.length > 0" class="title">
                     您有 {{ unpays.length }} 筆尚未繳費的報名紀錄.
                    <span class="show-data"> 溫馨提示：請提前繳費以確保您的上課名額 
                    </span>  
                  
                </h2>
                <h2 v-else class="title">
                    您的報名紀錄 
                </h2>  
              
            </div>
            
            
        </div>
        <div v-if="!noData" class="columns" style="padding-top:1em">
            <signup-table :signups="signups" 
            @remove="onRemove" @pay="onPay" @edit="onEdit">
            </signup-table>
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
            },
            unpays(){
                if(this.noData) return [];
                return this.signups.filter(item=>{
                    return parseInt(item.status) == 0;
                });
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
            },
            onPay(signup){
                window.location='/bills/' + signup.id
            },
            onEdit(signup){
                window.location='/signups/' + signup.id
            },
            
            
            
        },



    }
</script>

