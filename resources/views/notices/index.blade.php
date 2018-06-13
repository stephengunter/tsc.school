@extends('layouts.manage')


@section('content')


<notices-index v-show="indexMode" :init_model="model" :init_params="params"  
               :keys="keys"   :version="version" 
               v-on:selected="onSelected" v-on:quit="onQuit" v-on:create="onCreate" >
</notices-index>
<notices-details v-if="selected" :id="selected" v-on:saved="onUpdated"
                 v-on:back="backToIndex" v-on:deleted="backToIndex">
</notices-details>
<notices-create v-if="creating" :params="params"  v-on:cancel="backToIndex" v-on:saved="onCreated">
</notices-create>



@endsection

@section('scripts')

    <script type="text/babel">

        new Vue({
            el: '#main',
            data() {
                return {
                    version: 0,

                    model: {},

                   

                    keys: [],

                    params:{},

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

                this.params = {!! json_encode($params) !!} ;

                this.keys = {!! json_encode($keys) !!} ;
             

			},
            methods: {
                onCreate() {
                    this.creating = true;
                },
                onSelected(id,group) {
                    this.selected = id;

                    this.detailsMode='';
                },
                backToIndex() {
                    this.version += 1;

                    this.selected = 0;
                    this.creating = false;
                },
                onCreated() {
                   
                    this.backToIndex();
                },
                onUpdated(){
                    this.backToIndex();
                },
                onQuit(id){
                  
                    this.selected = id;
                    this.creating = false;
                }


			}

		});



    </script>


@endsection



