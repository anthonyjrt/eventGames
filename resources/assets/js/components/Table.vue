<template>
<div>

  <table  class="table table-striped">
  <thead class="thead-dark">
  <tr>
    <th v-for="key in columns"
        @click="sortBy(key)"
        :class="{ active: sortKey == key }">
      {{ $t(key.toString()) | capitalize }}
    </th>

    <th v-if="action == 1" colspan="1">Action</th>
  </tr>
  </thead>
  <tbody>
  <tr v-if="action == 1"><td v-for="(input, index) in formModel">
    <input type="text" v-if="index+1 == formModel.length && input.fName != 'select' && input.fName != 'console_id'" :name="input.fName" :id="input.fName" :placeholder="input.placeholder" class="form-control"

                                             v-model="form[input.fName]" v-on:keyup.enter="createModel" :loading="form.busy">
    <input v-else-if="input.fName != 'select' && input.fName != 'console_id'" type="text" :name="input.fName" :id="input.fName" :placeholder="input.placeholder" class="form-control" v-model="form[input.fName]">

    <multiselect v-if="input.fName == 'console_id'" v-model="form[input.fName]" :options="data3" placeholder="Sélectionner" label="libelle" track-by="id" @input="updateSimpleSelect"></multiselect>

    <multiselect v-if="input.fName == 'select'" v-model="form[input.fName]" selectedLabel="Sélectionné" deselectLabel="Déselectionner" selectLabel="Sélectionner" :options="data2" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Sélectionner" label="label" track-by="category_id" :preselect-first="true" @input="updateApprovers">
      <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" noResult="Pas de résultats!" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected</span></template>
    </multiselect>
  </td>
    <td v-if="formModel.length <= columns.length ? myActionColspan = 1 : 0" :colspan="myActionColspan">
    <td v-if="formModel.length < columns.length ? myActionColspan = (columns.length +1) - formModel.length : 0" :colspan="myActionColspan">
    </td>
  </tr>
  <tr v-for="(entry,index) in filteredData" :id="index">
    <td v-for="key in columns" >
      <a v-if="key == 'name'">{{entry[key] | uppercase}}</a>
      <a v-else-if="key == 'age'">{{entry[key]}}</a>
      <div v-else-if="key == 'categories'" v-for="category in entry.categories">
        <a>{{category.libelle}}</a>
      </div>

      <a v-else-if="key == 'console'">{{entry[key].libelle}}</a>
      <a v-else>{{entry[key] | capitalize}}</a>
    </td>
    <td v-if="action == 1">
      <button @click="initUpdate(entry, index)" :id="entry['id']" class="btn btn-success btn-xs" style="padding:8px"><span class="fa fa-edit" aria-hidden="true"></span></button>
      <modal
        :id="modal_id+entry['id']"
        :player_id="entry['id']"
        :update_modal="entry"
      :model="myModel"
      :games="data2"
      :channel="channel"></modal>
      <button @click="deleteModel(entry['id'])" class="btn btn-danger btn-xs" style="padding:8px"><span class="fa fa-trash"></span></button>
    </td>
  </tr>
  </tbody>
</table>

</div>

</template>

<script>

  var myModel = "";
  const getUrl = window.location;
  const baseUrl = getUrl .protocol + "//" + getUrl.host +"/api/";
  var arraySort = require('array-sort');
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

  import Form from 'vform';
  import _ from 'lodash';
  import Multiselect from 'vue-multiselect';
  import Modal from '../components/Modal';


  export default {
    // OR register locally
    components: { Multiselect,Modal },
  name: 'Table-demo',
  props: {
    columns: Array,
    filterKey: String,
    action:Boolean,
    myModel: String,
    myTModel: String,
    formModel: Array,
    childForm: Object
  },
  data: function () {
    var sortOrders = {}
    this.columns.forEach(function (key) {
      sortOrders[key] = 1

    })
    return {
      modal_id: "update_player_model",
      update_modal: {

      },
      value: null,
      approvers: [],
      simpleSelect: null,
      selectedCountries: [],
      countries: [],
      isLoading: false,
      sortKey: ['name'],
      sortOrder: ['asc'],
      formRanking: {},
      sortOrders: sortOrders,
      data: [],
      data2: [],
      data3: [],
      form: new Form(this.childForm) ,
      currentSort:'name',
      currentSortDir:'asc',
      sortSettings: [
        { 'name': true },
        {'firstname': true},
        { 'age': true }],
      asc: true,
      channel: null,
      index:null
    }
  },
  mounted()
  {
    this.channel = pusher.subscribe('players');
    this.channel.bind('player.created', (data) => {

      this.data.push(data);
    });

    this.mGames = this.update_modal.games;
    this.channel.bind('player.updated', (data) => {
      this.setRanking(data);
      this.data[this.index].games.push(data.games[data.games.length-1]);
      //this.update_modal.games.push(data);
    });
        this.read(this.myModel);

  },
  computed: {

    Sorted: function() {
      return _.orderBy(this.data, this.sortKey, this.sortOrder);
    },
    filteredData: function () {
      var sortKey = this.sortKey
      var filterKey = this.filterKey && this.filterKey.toLowerCase()
      var order = this.sortOrders[sortKey] || 1
      var data = this.data;

      if (filterKey.toString()) {
        data = data.filter(function (row) {
          return Object.keys(row).some(function (key) {
            return String(row[key]).toLowerCase().indexOf(filterKey) > -1
          })
        })
      }
      this.data = data;
      return data;

    }
  },
  filters: {
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
  },
  methods: {
    setRanking(player){
      axios.post(baseUrl+'ranking', player).then((response) => {
        var mymodel = this.myModel;
        this.ranking = response.data;
      })
    },
    updateApprovers(users) {
      let approvers = [];

      users.forEach((user) => {
        approvers.push(user.category_id);
      });

      this.approvers = approvers;

    },
    updateSimpleSelect(user) {
      let simpleSelect = null;

        simpleSelect = user.id;


      this.simpleSelect = simpleSelect;

    },
    limitText (count) {
      return `and ${count} other countries`
    },
    asyncFind (query) {
      this.isLoading = true
        this.countries = this.data2
        this.isLoading = false
    },
    clearAll () {
      this.selectedCountries = []
    },
    orderBy: function(sorKey) {
      this.sortKey = sorKey
      this.sortSettings[sorKey] = !this.sortSettings[sorKey]
      this.asc = this.sortSettings[sorKey]

    },
    sort:function(s) {
      //if s == current sort, reverse
      if(s === this.currentSort) {
        this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
      }
      this.currentSort = s;
    },
    sortBy: function(key) {
      if (key == this.sortKey) {
        this.sortOrder = (this.sortOrder == 'asc') ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortOrder = 'asc';
      }
    },
    // function for dynamic sorting
    compareValues: function(key, order='asc') {
    return function(a, b) {
      if(!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
        // property doesn't exist on either object
        return 0;
      }

      const varA = (typeof a[key] === 'string') ?
        a[key].toUpperCase() : a[key];
      const varB = (typeof b[key] === 'string') ?
        b[key].toUpperCase() : b[key];

      let comparison = 0;
      if (varA > varB) {
        comparison = 1;
      } else if (varA < varB) {
        comparison = -1;
      }
      return (
        (order == 'desc') ? (comparison * -1) : comparison
      );
    };
  },
    dynamicSort: function(property) {
    var sortOrder = 1;

    if(property[0] === "-") {
      sortOrder = -1;
      property = property.substr(1);
    }

    return function (a,b) {
      if(sortOrder == -1){
        return b[property].localeCompare(a[property]);
      }else{
        return a[property].localeCompare(b[property]);
      }
    }

  },

    read(model)
    {
      axios.get(baseUrl+model).then((response) => {
        var mymodel = this.myModel;
        if (response.data.modelRead){
          this.data = response.data.modelRead;
          console.log(this.data);

          this.data2 = response.data.modelRead2;
          if (response.data.modelRead3){
            this.data3 = response.data.modelRead3;
          }
        }
        else {
          this.data = response.data;
        }

        }
      )},
    async createModel()
    {
      if(this.form.birthdate)
        this.form.birthdate = this.backEndDateFormat(this.form.birthdate);
      if(this.approvers)
        this.form.select = this.approvers;
      this.form.console_id = this.simpleSelect;

      await this.form.post('/api/'+this.myModel)
      .then(response => {
        this.form = this.form.originalData;

        //this.data.push(response.data);
        //this.read(this.myModel);
        this.formRanking = this.form;
        this.form = new Form(this.childForm);
      })
      .catch((error) => {
        if( error.response ){

        }
      });
    },
    initUpdate(entry, index)

    {
      var modal = "#update_"+this.myModel+"_model"+entry.id;
      this.errors = [];
      this.update_modal = entry;
      this.index = index;
      $(modal).modal("show");


    },
    deleteModel(index)

    {

      let conf = confirm("Voulez-vous vraiment supprimer ce "+ this.myTModel +" ?");
      if (conf === true) {



        axios.delete(baseUrl+ this.myModel +'/' + index)

          .then(response => {

            this.read(this.myModel);
          })

          .catch(error => {



          });

      }

    },
    backEndDateFormat: function(date) {
      return moment(date, 'DD/MM/YYYY').format('YYYY-MM-DD');
    }

  }
}
</script>
