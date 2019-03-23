<template>
  <div class="container">
    <div v-if="data.length == 0" class="col-md-12">
      <h1>En attente du début du tournoi</h1>
    </div>

    <div v-else class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3><span class="glyphicon glyphicon-dashboard"></span> Rencontres</h3> <br>
        </div>
        <div class="panel-body">
          <table  class="table table-striped">
            <thead class="thead-dark">
            <tr>
              <th>Jeu</th>
              <th colspan="2" style="background-color: #C62907"></th>
              <th>VS</th>
              <th colspan="2" style="background-color: #1E50BB"></th>
              <th>En cours</th>
              <th>Résultat</th>
            </tr>
            </thead>
            <tbody  v-for="(entry, index) in data">
            <tr :id="entry.id">
              <td>
                <div v-for="r in entry.rankings[0]">{{ r.libelle}}</div>
              </td>
              <td>
                {{ entry.rankings[0].player_id}}
              </td>
              <td>
                <div v-for="r in entry.rankings[0]">
                  {{ r.name | uppercase}} {{ r.firstname | capitalize}}
                </div>
              </td>
              <td>VS</td>
              <td>
                  {{ entry.rankings[1].player_id}}
                </td>
              <td>
                <div v-for="r in entry.rankings[1]">
                  {{ r.name | uppercase}} {{ r.firstname | capitalize}}
                </div></td>
              <td>
                <input  v-if="entry.statut == 'Attente'" type="checkbox" :value="entry.statut" :id="entry.id"  @click="check($event, entry)">
                <input checked v-else-if="entry.statut == 'En cours'" type="checkbox" :value="entry.statut" :id="entry.id"  @click="check($event, entry)">
              </td>
              <td>
                <button class="btn-danger" @click="[result(entry.id,entry.rankings[0].id,entry.rankings[1].id, index), uncheck(entry.id)]">R</button>
                <button class="btn-primary" @click="[result(entry.id,entry.rankings[1].id,entry.rankings[0].id, index), uncheck(entry.id)]">B</button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  const getUrl = window.location;
  const baseUrl = getUrl .protocol + "//" + getUrl.host +"/api/";
  import Echo from "laravel-echo";

  window.Pusher = require('pusher-js');

  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '1f64241ae71172e1e018',
    cluster: 'eu'
  });
  var pusher = new window.Pusher('1f64241ae71172e1e018', {
    cluster: 'eu',
    forceTLS: true
  });

  /**window.Echo.channel('players')
   .listen('player.created', (e) => {
        alert(e.player.name);
      });*/



  window.Pusher.logToConsole = true;
  export default {
    name: "fight_control",
    middleware: 'auth',
    data: function () {
      return {
        data:[],
        data2:[],
        benef:'',
        b:null,
        counter:1

      }
    },
    mounted() {
      this.read('fight');
      this.channel = pusher.subscribe('fights');
      this.channel.bind('fight.finished', (data) => {
        console.log(this.data);
        this.data.push(data);
        console.log(this.data);
      });

    },filters: {
      uppercase: function (value){
        if (!value) return ''
        value = value.toString()
        return value.toUpperCase()
      },
      capitalize: function (value) {
        if (!value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
      },
    },computed: {
      reverse: function() {
        return this.data.sort();
      }
    },
    methods: {
      check: function(e, entry) {
        if (e.target.checked) {
          if (e.target.value == "Attente"){

            axios.patch(baseUrl+'fight/' + e.target.id, entry)

              .then(response => {
                console.log(response);
                //e.target.checked = false;
              })

              .catch(error => {

                this.errors = [];
                console.log(error);
              });
            //alert(e.target.value);
            //e.target.id
            //console.log(entry);
          }

        }
      },
      uncheck: function(checkbox){

      },
      removeRow: function(id) {
        this.$delete(this.data, id)
      },
      result: function(id, win, loose, index) {
    axios.patch(baseUrl+'fight/' + id, {
          win: win,
          loose:loose
        })

          .then(response => {
            this.removeRow(index);
            console.log(response);
          })

          .catch(error => {

            this.errors = [];
            console.log(error);
          });

      },
      async read(model)
      {
        await   axios.get(baseUrl+'control').then((response) => {

            var mymodel = this.myModel;
            if (response.data.modelRead){
              console.log('raté');

            }
            else {
              this.data = response.data.fights;
              console.log(this.data);
            }
          }
        )
      },
    }
  }
</script>

<style scoped>

</style>
