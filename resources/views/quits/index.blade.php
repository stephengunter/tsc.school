@extends('layouts.manage')


@section('content')


<quits-index v-show="indexMode" :init_model="model" :summary_model="summary" :init_params="params" :centers="centers"  :statuses="statuses" 
    :can_review="can_review" :version="version"
    v-on:selected="onSelected" >
</quits-index>
<quits-details v-if="selected" :id="selected"
    v-on:back="backToIndex" v-on:signup-deleted="backToIndex">
</quits-details>



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

                    params:{},
                    
                    centers: [],

                    statuses:[],

                    can_review: false,
                   
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
                this.summary = {!! json_encode($summary) !!} ;
                this.params = {!! json_encode($params) !!} ;

                this.centers = {!! json_encode($centers) !!} ;
                this.statuses = {!! json_encode($statuses) !!} ;
              

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


			}

		});



    </script>


@endsection



