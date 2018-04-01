@extends('layouts.client')

@section('content')

 
<home-view v-on:loaded="onLoaded" :notices_model="notices_model" :latest_courses="latest_courses" :recommend_courses="recommend_courses">

</home-view>



@endsection





@section('scripts')

<script type="text/babel">


new Vue({
    el: '#main',
    data() {
        return {
            notices_model:{},
            latest_courses:{},
            recommend_courses:{}
        }
    },
    beforeMount() {
        

        this.notices_model = {!! json_encode($noticesModel) !!} ;
        this.latest_courses = {!! json_encode($latestCourses) !!} ;
        this.recommend_courses = {!! json_encode($recommendCourses) !!} ;
    },
    methods: {
        onLoaded(){
            onPageLoaded();
        }
    }

});



</script>

@endsection