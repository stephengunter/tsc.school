@extends('layouts.manage')


@section('content')


<signups-index v-show="indexMode" :init_model="model" :summary_model="summary" :init_params="params"  
               :terms="terms" :centers="centers" :courses="courses" :statuses="statuses"
               :version="version" :can_quit="canQuit"
               v-on:selected="onSelected" v-on:quit="onQuit" v-on:create="onCreate" 
               v-on:import="beginImport">
</signups-index>
<signups-details v-if="selected" :id="selected" :payways="counter_payways" :mode="detailsMode"
                 v-on:back="backToIndex" v-on:signup-deleted="backToIndex">
</signups-details>
<signups-create v-if="creating" :params="params"  v-on:cancel="backToIndex" v-on:saved="onCreated">
</signups-create>


<signups-import v-if="importing"  :can_import="can_import"  
                v-on:cancel="backToIndex" v-on:imported="backToIndex">
</signups-import>


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

                    terms: [],
                    centers: [],
                    courses: [],

                    statuses:[],

                    canQuit:false,

                    counter_payways:[],

                    params:{},

                    creating:false,
                    selected: 0,

                    importing: false,

                    detailsMode:''

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
                this.summary = {!! json_encode($summary) !!} ;

                this.params = {!! json_encode($params) !!} ;

                this.terms = {!! json_encode($terms) !!} ;
                this.centers = {!! json_encode($centers) !!} ;
                this.courses = {!! json_encode($courses) !!} ;
                this.statuses = {!! json_encode($statuses) !!} ;
               
                this.can_import = Helper.isTrue('{!! $canImport !!}');  

                this.canQuit = {!! json_encode($canQuit) !!} ;
              
                this.counter_payways = {!! json_encode($counterPayways) !!} ;

			},
            methods: {
                onCreate(params) {
                    this.creating = true;
                    this.params = { ...params }
                },
                onSelected(id,group) {
                    this.selected = id;

                    this.detailsMode='';
                },
                beginImport() {

                    this.importing = true;
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                    this.importing = false;

                    this.detailsMode='';
                },
                onCreated(signup) {
                    this.selected = signup.id;
                    this.creating = false;
                    this.detailsMode='create';
                },
                onQuit(id){
                  
                    this.selected = id;
                    this.creating = false;
                    this.detailsMode='quit';
                }


			}

		});



    </script>


@endsection



