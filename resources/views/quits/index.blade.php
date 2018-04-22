@extends('layouts.manage')


@section('content')


<quits-index v-show="indexMode" :init_model="model"  :centers="centers"  :statuses="statuses" :payways="payways"
    :can_review="can_review" :can_finish="can_finish" :version="version"
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
                    
                    centers: [],

                    statuses:[],
                    payways:[],

                    can_review: false,
                    can_finish: false,
                   
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
                
                
                this.centers = {!! json_encode($centers) !!} ;
                this.statuses = {!! json_encode($statuses) !!} ;
                this.payways = {!! json_encode($payways) !!} ;

                this.can_review = Helper.isTrue('{!! $canReview !!}');  
                this.can_finish = Helper.isTrue('{!! $canFinish !!}');  

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



