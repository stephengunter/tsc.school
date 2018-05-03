@extends('layouts.manage')


@section('content')


<payrolls-index v-show="indexMode" :init_model="model" :init_params="params" :centers="centers" :years="years" :months="months"
                :can_review="can_review" :can_finish="can_finish" :version="version"
                v-on:selected="onSelected">
</payrolls-index>
<payrolls-details v-if="selected" :id="selected"  
                 v-on:back="backToIndex" v-on:payroll-deleted="backToIndex">
</payrolls-details>



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
                    years: [],
                    months: [],

                    params:{
                        year:0,
                        month:0
                    },

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
                this.years = {!! json_encode($years) !!} ;
                this.months = {!! json_encode($months) !!} ;

                let year = {!! json_encode($year) !!} ;
                let month = {!! json_encode($month) !!} ;
                this.params.year=year;
                this.params.month=month;

                this.can_review = Helper.isTrue('{!! $canReview !!}');
                this.can_finish = Helper.isTrue('{!! $canFinish !!}');  
			},
            methods: {
                
                onSelected(id) {
                    this.selected = id;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                }


			}

		});



    </script>


@endsection



