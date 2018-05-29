@extends('layouts.client')

@section('content')

 
<notice-details  :init_model="model"  v-on:loaded="onLoaded" > 
             
</notice-details>
             
             



@endsection





@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            model: {},
			
        }
    },
    beforeMount() {
       
        this.model = {!! json_encode($notice) !!};
       
    },
    methods: {
        onLoaded(){
            onPageLoaded();
        }
    }

});



</script>

@endsection