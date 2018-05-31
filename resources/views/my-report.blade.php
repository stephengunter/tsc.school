@extends('layouts.manage')

@section('content')


    <my-reports-index></my-reports-index>

@endsection




@section('scripts')

<script type="text/babel">

   new Vue({
       el: '#main',
       data() {
           return {
               model: null,
               can_edit:false,
               can_import: false,


               importing:false

           }
       },
       computed: {
           indexMode() {
            
               if (this.importing) return false;

               return true;
           }
       },
       beforeMount() {
          
       },
       methods: {
           beginImport() {
               this.importing = true;
           },
           backToIndex() {
               this.$refs.categoryIndex.fetchData();
               this.importing = false;
           }
       }

   });



</script>


@endsection
