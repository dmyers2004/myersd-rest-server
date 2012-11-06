mvc.controller_index_method_index = {

  __construct: function() {
  	mvc.log('controller_login_method_index constructor');
  },
  
  btnsignin: {
    click: function() {
      mvc.redirect(mvc.host + '/todo');
    }
  }

};

/*
mvc.ajax.auth_user = 'login@login.com';
mvc.ajax.auth_pw = 'login';
mvc.ajax.auth = true;

var settings = {};
settings.url = 'http://localhost/myrestserver/login';
settings.type = 'post';

$.mvcAjax(settings);
*/
