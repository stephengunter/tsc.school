<template>
   
    
    <div class="tile is-ancestor">
        <div class="tile is-vertical">
            <div class="tile">
                <div class="tile is-parent is-vertical" style="font-size:16px">
                    <div class="columns" >
                        <div class="column" >
                            <label class="label label-title">姓名</label>
                            <p>
                                {{  user.profile.fullname }}
                            </p>
                        </div>
                        <div class="column" >
                            <label class="label label-title">Email</label>
                            <p>
                                {{  user.email }}
                            </p>
                        </div>
                        <div class="column" >
                            <label class="label label-title">電話</label>
                            <p>
                                {{  user.phone }}
                            </p>
                            
                        </div>
                
                    </div>   <!--  End Row -->
                    <div class="columns" >
                        
                        <div class="column" >
                            <label class="label label-title">性別</label>
                            <p>
                                {{ user.profile.gender | genderText  }}
                            </p>
                            
                        </div>
                        <div class="column" >
                            <label class="label label-title">身分證號</label>
                            <p>
                                {{ user.profile.sid }}
                            </p>
                        </div>
                        <div class="column" >
                            <label class="label label-title">生日</label>
                            <p>
                                    {{ user.profile.dob }}
                            </p>
                        </div>
                    </div>  <!--  End Row -->
                    
                </div>
            
            </div>    
        </div>
    </div>
	
    
</template>

<script>
    export default {
        name: 'ShowProfile', 
        props: {
            user: {
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
