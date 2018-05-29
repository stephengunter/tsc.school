@extends('layouts.client')

@section('content')

 
<notices-index  :init_model="list"  v-on:loaded="onLoaded" > 
             
</notices-index>
             
             



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