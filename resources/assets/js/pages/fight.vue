<template>
  <div class="container">
    <div v-if="data.length == 0" class="col-md-12">
      <h1>En attente de match</h1>
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
              <th style="background-color: #C62907"></th>
              <th>VS</th>
              <th style="background-color: #1E50BB"></th>
              <th>statut</th>
            </tr>
            </thead>
            <tbody  v-for="entry in data">
            <tr :id="entry.id">
              <td>
                <div v-for="r in entry.rankings[0]">{{ r.libelle}}</div>
              </td>
              <td>
                <div v-for="r in entry.rankings[0]">
                  {{ r.name | uppercase}} {{ r.firstname | capitalize}}
                </div>
              </td>
              <td>VS</td>
              <td>
                <div v-for="r in entry.rankings[1]">
                  {{ r.name | uppercase}} {{ r.firstname | capitalize}}
                </div></td>
              <td>{{ entry.statut }}</td>

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
        name: "fight",
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
      this.channel.bind('fight.updated', (data) => {
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
      removeRow: function(id) {
        this.$delete(this.data, id)
      },
      async read(model)
      {
        await   axios.get(baseUrl+model).then((response) => {

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
