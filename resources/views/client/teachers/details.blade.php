@extends('layouts.client')

@section('content')

<teacher-details :init_model="teacher"></teacher-details>

@endsection


@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            teacher:null
        }
    },
    beforeMount() {
        this.teacher = {!! json_encode($teacher) !!} ;
               
    },
    mounted(){
        onPageLoaded();
    },
    methods: {
        
    }

});



</script>

@endsection
