<template>
       <div class="modal fade" tabindex="-1" role="dialog" :id="this.id">

             <div class="modal-dialog" role="document">

                 <div class="modal-content">

                     <div class="modal-header">

                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span

                                 aria-hidden="true">&times;</span></button>

                         <h4 class="modal-title">Editer Joueur</h4>

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

                                                                      v-model="update_modal.name">

                         </div>
                         <div class="form-group">

                             <label>{{ $t('firstname')}}:</label>

                             <input type="text" placeholder="Player Name" class="form-control"

                                                                      v-model="update_modal.firstname">

                         </div>
                         
<div class="form-group">

                             <label>{{ $t('birthdate')}}:</label>

                             <input type="text" placeholder="Player Name" class="form-control"

                                                                      v-model="update_modal.birthdate">

                         </div>

  <div class="form-group" >
    <table class="table table-striped">
      <thead class="thead-dark">
      <tr>
        <th>Jeux</th>
        <th>Vies</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>
          <multiselect v-model="update_modal.selectgame" :options="games"label="label"></multiselect>
        </td>
        <td><multiselect v-model="update_modal.selectlife" :options="lifeOptions" placeholder="Sélectionner"></multiselect></td>
      </tr>
      <tr v-for="g in mGames">
        <td>{{ g.libelle }}</td>
        <td>{{ g.pivot.life }}</td>
      </tr>
      </tbody>
    </table>

                           </div>

                     </div>

                     <div class="modal-footer">

                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                         <button type="button" @click="updatePlayer" class="btn btn-primary">Submit</button>

                     </div>

                 </div><!-- /.modal-content -->

             </div><!-- /.modal-dialog -->

         </div><!-- /.modal -->
</template>

<script>
  import Multiselect from 'vue-multiselect'
  const getUrl = window.location;
  const baseUrl = getUrl .protocol + "//" + getUrl.host +"/api/";

    export default {
      components: { Multiselect },
        name: "Modal",
      props: {
          id: String,
        player_id: Number,
        update_modal: Object,
        model: String,
        games:Array,
        channel:window.Pusher
      },
      mounted(){

    this.mGames = this.update_modal.games;
        /*this.channel.bind('player.updated', (data) => {
          //this.setRanking(data);
          this.mGames.push(data);
          //this.update_modal.games.push(data);
        });*/
      },
      data: function () {
        return {
          value: null,
          test: null,
          options: ['list', 'of', 'options'],
          lifeOptions: [1,2,3],
          errors: [],
          mGames:[]
        }
      },
      methods: {

        async updatePlayer()

        {
          var serverDate = this.backEndDateFormat(this.update_modal.birthdate);
          var update = {
            lastname: this.update_modal.name,
            firstname: this.update_modal.firstname,
            birthdate: serverDate,
            select: [
              this.update_modal.selectgame.game_id,
              this.update_modal.selectlife]
          };


          await axios.patch(baseUrl+this.model+'/' + this.update_modal.id, update)

          .then(response => {

            //this.read();
            this.update_modal.selectgame = null;
            this.update_modal.selectlife=null;
//              $("#update_player_model"+ this.update_modal.id).modal("hide");



          })

          .catch(error => {

            this.errors = [];
            console.log(error);
          });

        },
        frontEndDateFormat: function(date) {
          return moment(date, 'YYYY-MM-DD').format('DD/MM/YYYY');
        },
        backEndDateFormat: function(date) {
          return moment(date, 'DD/MM/YYYY').format('YYYY-MM-DD');
        }
      }}

</script>

<style scoped>

</style>
