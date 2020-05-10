window._ = require('lodash');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');

Vue.config.errorHandler = function (err, vm) {
  window.console.log(err);
};

var CommodityUpdate = new Vue({
    el: "#commodity-update",
    data: {
        data: [
        ],
    },
    methods: {
      getData: function(id){
        self = this;
        window.$.ajax({
          url: window.$("#commodity-update").attr('data-member'),
          type: 'POST',
          data: {_token: window.Laravel.csrfToken, id: id},
        })
        .done(function(response) {
            self.data = response.data;   
        });
        
      }
    },
});
window.CommodityUpdate = CommodityUpdate;