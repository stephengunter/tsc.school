@extends('layouts.client')

@section('content')

 
<centers-index  :areas="areas" :overseas="overseas" v-on:loaded="onLoaded" > 
             
</centers-index>
             
             



@endsection





@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            areas: [],
			overseas:[]	
        }
    },
    beforeMount() {
       
        this.areas = {!! json_encode($areas) !!};
        this.overseas = {!! json_encode($overseas) !!};
    },
    methods: {
        onLoaded(){
            onPageLoaded();
        }
    }

});



</script>

@endsection