@extends('layouts.client')

@section('content')

 
<courses-index  :model="list"  v-on:loaded="onLoaded" > 
             
</courses-index>
             
             



@endsection





@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            list: {},
			
        }
    },
    beforeMount() {
       
        this.list = {!! json_encode($list) !!};
       
    },
    methods: {
        onLoaded(){
            onPageLoaded();
        }
    }

});



</script>

@endsection