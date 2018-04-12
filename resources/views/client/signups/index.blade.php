@extends('layouts.client')

<style>
    
.media-content{
    font-size: 1.2em
}

</style>
@section('content')

<signup-index-view :model="signups">

</signup-index-view>


@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            signups:[]
        }
    },
    beforeMount() {
        this.signups = {!! json_encode($signups) !!};
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
