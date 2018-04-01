@extends('layouts.manage')

@section('content')

 <home-view :systems="systems"></home-view>

@endsection





@section('scripts')

<script type="text/babel">

new Vue({
    el: '#main',
    data() {
        return {
            systems: []
        }
    },
    beforeMount() {
        let systemModel = {!! json_encode($systems) !!} ;
    
        this.systems = Object.keys(systemModel).map(i => systemModel[i]);
       
    },
    methods: {

    }

});



</script>

@endsection