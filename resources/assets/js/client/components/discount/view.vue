<template>
    <div class="panel" >
        <div class="panel-heading panel-title heading" >
            優惠辦法
        </div>
        <div class="panel-block">
            <table class="table is-bordered is-striped ">
                <thead>
                    <tr>
                        <th style="width:60%;text-align: center;">
                        身分別
                        </th>
                        <th style="width:20%;text-align: center;">
                            {{ bird_date_text }} 前報名繳費
                        </th>
                        <th style="width:20%;text-align: center;">
                            {{ bird_date_text }} 後
                        </th>
                    </tr>
                </thead>
        
                <tbody v-if="center" style="font-size:17px"  >
                    <tr v-for="(discount,index) in center.discounts" :key="index">
                        <td>
                            {{ discount.name }}
                        </td>
                    
                        <td style="text-align: center;" v-html="pointsText(discount.pointOneText)"> </td>    
                        <td style="text-align: center;" v-html="pointsText(discount.pointTwoText)">  </td>     
                    
                        
                    </tr>
                    
                </tbody>
            </table>
        
        </div>


    </div>
    
</template>

<script>
  
    export default {
        name:'DiscountView',
        props: {
            center:{
               type: Object,
               default: null
            },
            bird_date_text: {
                type: String,
                default: ''
            },
        },
        beforeMount(){
            
        },
        data(){
            return{
               
                
            }
        },
        computed:{
           
        },
        methods:{
            pointsText(val){
                if(!val) return '不優惠';
                return val;
            }
        },
        
    }
</script>