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

var Subscription = new Vue({
    el: "#subscription",
    data: {
        data: [
        ],
    },
    methods: {
      getData: function(id){
        self = this;
        window.$.ajax({
          url: window.$("#subscription").attr('data-subscription'),
          type: 'POST',
          data: {_token: window.Laravel.csrfToken, order_id: id},
        })
        .done(function(response) {
            self.data = response.data;   
        });
        
      }
    },
});
window.Subscription = Subscription;