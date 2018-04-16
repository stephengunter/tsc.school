@extends('layouts.manage')


@section('content')


<students-index v-show="indexMode" :init_model="model" :terms="terms" :centers="centers" :courses="courses" 
                :can_edit_scores="canEditScores"
                :version="version"   v-on:selected="onSelected"  >
             
</students-index>
<students-details v-if="selected" :id="selected" 
                 v-on:back="backToIndex" >  
</students-details>




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
                    centers: [],

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
              
                this.canEditScores = {!! json_encode($canEditScores) !!} ;    
			},
            methods: {
                onSelected(id) {
                    this.selected = id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                   
                },


			}

		});



    </script>


@endsection



