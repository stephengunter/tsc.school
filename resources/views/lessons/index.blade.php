@extends('layouts.manage')


@section('content')


<lessons-index v-show="indexMode" :init_model="model" :terms="terms" :centers="centers" :courses="courses" 
               :version="version"
               v-on:selected="onSelected" v-on:create="onCreate" >
</lessons-index>
<lessons-details v-if="selected" :id="selected" :payways="counter_payways" 
                 v-on:back="backToIndex" v-on:lesson-deleted="backToIndex">
</lessons-details>
<lessons-create v-if="creating" :course="courseId" v-on:cancel="backToIndex" v-on:saved="onCreated">
</lessons-create>



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

                  

                    courseId:0,

                    creating:false,
                    selected: 0,

                    

                }
            },
            computed: {
                indexMode() {
                    if (this.creating) return false;
                    if (this.selected) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
                this.courses = {!! json_encode($courses) !!} ;
               

			},
            methods: {
                onCreate(courseId) {
                    this.creating = true;
                    this.courseId = parseInt(courseId);
                },
                onSelected(id,group) {
                    this.selected = id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                },
                onCreated(lesson) {
                    this.selected = lesson.id;
                    this.creating = false;
                }


			}

		});



    </script>


@endsection



