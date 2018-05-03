@extends('layouts.client')

@section('content')


@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
           
            err:''
        }
    },
    beforeMount() {
        this.err= {!! json_encode($err) !!};
        Bus.$emit('errors','此課程目前無法報名,或者您已經報名過此課程.');

        window.setTimeout(()=>{ 
               window.location='/signups';
            }, 1000);
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
