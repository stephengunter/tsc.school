<template>
   
    
    <div class="tile is-ancestor">
        <div class="tile is-vertical">
            <div class="tile">
                <div class="tile is-parent is-vertical" style="font-size:16px">
                    <div class="columns" >
                        <div class="column" >
                            <label class="label label-title">姓名</label>
                            <p>
                               {{ teacher.user.profile.fullname  }}
                            </p>
                        </div>
                        <div class="column" >
                            <label class="label label-title">專長</label>
                            <p>
                                {{ teacher.specialty  }}
                            </p>
                        </div>
                        <div class="column" >
                            <label class="label label-title">最高學歷 </label>
                            <p>
                                 {{ teacher.education  }}
                            </p>
                            
                        </div>
                
                    </div>   <!--  End Row -->
                    <div class="columns" >
                        
                        <div class="column is-one-third" >
                            <label class="label label-title">經歷</label>
                            <p v-html="teacher.experiences"></p>      
                            
                        </div>
                        <div class="column" >
                            <label class="label label-title">個人簡介</label>
                            <p v-html="teacher.description"></p> 
                        </div>
                        
                    </div>  <!--  End Row -->
                    
                </div>
            
            </div>    
        </div>
    </div>
	
    
</template>

<script>
    export default {
        name: 'ShowTeacher', 
        props: {
            teacher: {
              type: Object,
              default: null
            },
            can_edit:{
               type: Boolean,
               default: true
            },            
            can_back:{
              type: Boolean,
              default: true
            },
            version: {
              type: Number,
              default: 0
            },
        },
        data() {
            return {

            }
        },
        computed:{
            
        }, 
        methods: { 
            
            
            
        }
    }
</script>
