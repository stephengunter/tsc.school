@extends('layouts.manage')


@section('content')


<courses-index v-show="indexMode" :init_model="model" :terms="terms" :centers="centers" :categories="categories"
               :can_review="can_review" :can_import="can_import" :version="version"
                v-on:selected="onSelected" v-on:create="onCreate" v-on:import="beginImport">
</courses-index>
<courses-details v-if="selected" :id="selected"  :weekdays="weekdays"  :percents_options="percents_options"
                  v-on:back="backToIndex" v-on:course-deleted="backToIndex">
</courses-details>
<courses-create v-if="creating" v-on:cancel="backToIndex" v-on:saved="onCreated">
</courses-create>

<courses-import v-if="importing" :can_import="can_import" :terms="terms" :centers="centers"
                 v-on:cancel="backToIndex" v-on:imported="backToIndex">
</courses-import>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},
                    can_review: false,
                    can_import: false,

                    terms: [],
                    centers: [],
                    categories: [],
                    weekdays:[],

                    percents_options:[],

                    creating:false,
                    selected: 0,

                    importing: false,

                    

                }
            },
            computed: {
                indexMode() {
                    if (this.creating) return false;
                    if (this.selected) return false;
                    if (this.importing) return false;

                    return true;
                }
            },
            beforeMount() {
                this.model = {!! json_encode($list) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
                this.categories = {!! json_encode($categories) !!} ;

                this.weekdays = {!! json_encode($weekdays) !!} ;
                
                this.can_review = Helper.isTrue('{!! $canReview !!}');  
                this.can_import = Helper.isTrue('{!! $canImport !!}'); 
                
                this.percents_options = {!! json_encode($percentsOptions) !!} ;  
                
			},
            methods: {
                onCreate() {
                    this.creating = true;
                },
                onSelected(id) {
                    this.selected = id;
                },
                beginImport() {

                    this.importing = true;
                },
                onCreated(course){
                    this.creating = false;
                    this.selected = course.id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                    this.importing = false;
                }


			}

		});



    </script>


@endsection



