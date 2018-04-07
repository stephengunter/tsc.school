@extends('layouts.client')

@section('content')

 
<courses-details  :init_model="model"  v-on:loaded="onLoaded" > 
             
</courses-details>
             
             



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
       
        this.model = {!! json_encode($model) !!};
       
    },
    methods: {
        onLoaded(){
            onPageLoaded();
        }
    }

});



</script>

@endsection