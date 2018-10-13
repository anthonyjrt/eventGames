<template>

     <div class="container">

  <div class="row">

             <div class="col-md-12">

                 <div class="panel panel-default">

                     <div class="panel-heading">

                  <h3><span class="glyphicon glyphicon-dashboard"></span> {{ $t('players_list')}}</h3> <br>

                     </div>



                     <div class="panel-body">
    <form id="search" class="pull-right">
      {{ $t('search')}} <input name="query" v-model="searchQuery"><br/><br/>
    </form>
    <table-demo
      :myModel="myModel"
      :columns="gridColumns"
      :filter-key="searchQuery"
    :action="true"
    :my-t-model="$t(myModel)"
    :form-model="formModel"
    :child-form="childForm">
    </table-demo>
  <!--              <table class="table table-striped" v-if="players">
    <thead class="thead-dark">

                               <tr>
      <th scope="col">{{ $t('name')}}</th>
      <th scope="col"> {{ $t('firstname')}}</th>
      <th scope="col" data-toggle="tooltip" data-placement="top"  :title="$t('calculate_from_birthdate')">{{ $t('birthdate')}}</th>
      <th scope="col">{{ $t('inGame')}}</th>
      <th colspan="2">Action</th>
                                 </tr>
    </thead>
                             <tbody>

<tr>
  <td>                             <input type="text" name="name" id="name" placeholder="Player Name" class="form-control"

                                                                            v-model="form.name">
  </td>
  <td>                           <input type="text" name="firstname" id="firstname" :placeholder="$t('firstname')" class="form-control"

                                                                          v-model="form.firstname">
  </td>
  <td>
    <input type='text' name="birthdate" id="birthdate" class="form-control" :placeholder="$t('date_format')" :loading="form.busy" v-model="form.birthdate" v-on:keyup.enter="createPlayer"/>
    </td><td colspan="2"></td>
</tr>
                           <tr v-for="player in gridData">
  <td>{{ player.name | uppercase }}</td>
  <td>{{ player.firstname | lowercase | capitalize }}</td>
  <td>{{ player.age }}</td>
  <td>{{ player.inGame }}</td>

                                 

                                 <td>
  <button @click="initUpdate(player)" :id="player.id" class="btn btn-success btn-xs" style="padding:8px"><span class="glyphicon glyphicon-edit"></span></button>

  <!--<button @click="deletePlayer(player.id)" class="btn btn-danger btn-xs" style="padding:8px"><span class="glyphicon glyphicon-trash"></span></button>

                                 </td>

                             </tr>

                           </tbody>

                         </table>-->

                     </div>

                 </div>

             </div>

         </div>







         <div class="modal fade" tabindex="-1" role="dialog" id="update_player_model">

             <div class="modal-dialog" role="document">

                 <div class="modal-content">

                     <div class="modal-header">

                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

                                 aria-hidden="true">&times;</span></button>

                         <h4 class="modal-title">Update Player</h4>

                     </div>

                     <div class="modal-body">



                         <div class="alert alert-danger" v-if="errors.length > 0">

                             <ul>

                                 <li v-for="error in errors">{{ error }}</li>

                             </ul>

                         </div>



                         <div class="form-group">

                             <label>{{$t('name')}}:</label>

                             <input type="text" placeholder="Player Name" class="form-control"

                                                                      v-model="update_player.name">

                         </div>
                         <div class="form-group">

                             <label>{{ $t('firstname')}}:</label>

                             <input type="text" placeholder="Player Name" class="form-control"

                                                                      v-model="update_player.firstname">

                         </div>
                         <div class="form-group">

                             <label>{{ $t('birthdate')}}:</label>

                             <input type="text" placeholder="Player Name" class="form-control"

                                                                      v-model="update_player.birthdate">

                         </div>

                     </div>

                     <div class="modal-footer">

                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                         <button type="button" @click="updatePlayer" class="btn btn-primary">Submit</button>

                     </div>

                 </div><!-- /.modal-content -->

             </div><!-- /.modal-dialog -->

         </div><!-- /.modal -->



     </div>

</template>



<script>
   import TableDemo from "../components/Table";
   const getUrl = window.location;
   const baseUrl = getUrl .protocol + "//" + getUrl.host +"/api/";

  import Form from 'vform'
   import VButton from "../components/Button";
  export default {
    components: {VButton, TableDemo},
    middleware: 'auth',

    metaInfo () {
      return { title: this.$t('players_list') }
    },
    data(){

      return {

        form: new Form( {
          name: '',
          firstname: '',
          birthdate:''

        }),
        childForm: {
          name: '',
          firstname: '',
          birthdate:''

        },

        errors: [],

        players: [],

        update_player: {},
        searchQuery: '',
        gridColumns: ['name', 'firstname', 'age', 'inGame'],
        gridData: [],
        myModel: "player",
        formModel: [
          { fName: 'name', placeholder: 'Votre Nom', model:'form.name'},
          { fName: 'firstname', placeholder: 'Votre Prénom', model:'form.firstname'},
          { fName: 'birthdate', placeholder: 'JJ/MM/AAAA', model:'form.birthdate'},
        ]

      }

    },

    mounted()

    {

      this.readPlayers("player");

    },
    filters: {
      capitalize: function (value) {
        if (!value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      },
      uppercase: function (value){
        if (!value) return ''
        value = value.toString()
        return value.toUpperCase()
      },
      lowercase: function (value){
        if (!value) return ''
        value = value.toString()
        return value.toLowerCase()
      }
    },
    computed: {
      isDisabled() {
        // evaluate whatever you need to determine disabled here...
        return this.form.validated;
      }
    },
    methods: {
      /*deletePlayer(index)

      {

        let conf = confirm("Voulez-vous vraiment supprimer ce joueur?");
        if (conf === true) {



          axios.delete(baseUrl+'player/' + index)

            .then(response => {
              this.readPlayers();
            })

            .catch(error => {



            });

        }

      },*/

      initAddPlayer()

      {

        $("#add_player_model").modal("show");

      },

      async createPlayer()

      {
        this.form.birthdate = this.backEndDateFormat(this.form.birthdate);
        console.log(this.form.birthdate);
        await this.form.post('/api/player')
          .then(response => {
            this.reset();
            console.log(response.data)
            this.players.push(response.data);
            $("#add_player_model").modal("hide");
            this.readPlayers();
          })
          .catch((error) => {
            if (error.response.status === 403) {
              console.log(error.response.data.message);
              this.reset();
              $("#add_player_model").modal("hide");
            }

            else if( error.response ){
              console.log(error.response.data); // => the response payload
            }
          });
      },

      reset()
      {
        this.form.name = '';
        this.form.firstname = '';
        this.form.birthdate = '';
        this.form.inGame = '';
      },

        readPlayers(model)
      {
        axios.get(baseUrl+model).then((response) => {
            this.gridData = response.data;
      }
      )},

      initUpdate(index)

      {

        this.errors = [];

        $("#update_player_model").modal("show");
        this.update_player = index;


        console.log(this.update_player);

      },

      updatePlayer()

      {
        var serverDate = this.backEndDateFormat(this.update_player.birthdate);
        console.log(this.update_player);
        axios.patch(baseUrl+'player/' + this.update_player.id, {

          name: this.update_player.name,
          firstname: this.update_player.firstname,
          birthdate: serverDate

        })

          .then(response => {



            $("#update_player_model").modal("hide");
            this.readPlayers();


          })

          .catch(error => {

            this.errors = [];

            if (error.response.data.errors.name) {

              this.errors.push(error.response.data.errors.name[0]);

            }



            if (error.response.data.errors.description) {

              this.errors.push(error.response.data.errors.description[0]);

            }

          });

      },
      frontEndDateFormat: function(date) {
        return moment(date, 'YYYY-MM-DD').format('DD/MM/YYYY');
      },
      backEndDateFormat: function(date) {
        return moment(date, 'DD/MM/YYYY').format('YYYY-MM-DD');
      }

    }

  }

</script>
