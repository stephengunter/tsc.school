@extends('layouts.manage')


@section('content')


<lessons-index v-show="indexMode" :init_model="model" :terms="terms" :centers="centers" :courses="courses" 
                :can_review="can_review" :version="version"
               v-on:selected="onSelected">
</lessons-index>
<lessons-details v-if="selected" :id="selected"  
                 v-on:back="backToIndex" v-on:lesson-deleted="backToIndex">
</lessons-details>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},

                    terms: [],
                    centers: [],
                    courses: [],

                    can_review: false,

                    courseId:0,

                    creating:false,
                    selected: 0,

                    

                }
            },
            computed: {
                indexMode() {
                    
                    if (this.selected) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
                this.courses = {!! json_encode($courses) !!} ;
               
                this.can_review = Helper.isTrue('{!! $canReview !!}');  
			},
            methods: {
                
                onSelected(id,group) {
                    this.selected = id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                },
                onCreated(lesson) {
                    this.selected = lesson.id;
                }


			}

		});



    </script>


@endsection



