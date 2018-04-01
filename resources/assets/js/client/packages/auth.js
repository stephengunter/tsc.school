
export default function(Vue){
   Vue.auth={
       
       setToken(token) {
           
         axios.defaults.headers.common.Authorization='Bearer ' + token;

       }

   }
  

   Object.defineProperties(Vue.prototype, {
       $auth:{
           get:()=>{
               return Vue.auth
           }
       }
   })
}