@extends('layouts.manage')


@section('content')


<signups-index v-show="indexMode" :init_model="model" :summary_model="summary" :terms="terms" :centers="centers" :courses="courses" :statuses="statuses"
               :version="version"
               v-on:selected="onSelected" v-on:create="onCreate" >
</signups-index>
<signups-details v-if="selected" :id="selected"
                 v-on:back="backToIndex" v-on:signup-deleted="backToIndex">
</signups-details>
<signups-create v-if="creating" :course="courseId" v-on:cancel="backToIndex" v-on:saved="onCreated">
</signups-create>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    summary:{},

                    terms: [],
                    centers: [],
                    courses: [],

                    statuses:[],

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
                this.summary = {!! json_encode($summary) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
                this.courses = {!! json_encode($courses) !!} ;
                this.statuses = {!! json_encode($statuses) !!} ;
              
              

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
                onCreated(signup) {
                    this.selected = signup.id;
                    this.creating = false;
                }


			}

		});



    </script>


@endsection



