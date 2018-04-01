@extends('layouts.client')

@section('content')

  
    <p  v-if="model" v-html="model.content">

    </p>



@endsection





@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
           model:null
        }
    },
    beforeMount() {
        this.model = {!! json_encode($model) !!};
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection