@extends('layouts.client')

@section('content')

 


@endsection





@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            msg:'',
        }
    },
    beforeMount() {

        this.msg = {!! json_encode($msg) !!} ;
    },
    methods: {
        onLoaded(){
            onPageLoaded();
        }
    }

});



</script>

@endsection