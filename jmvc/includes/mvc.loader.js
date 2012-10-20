mvc.load = {

	/* load a file */
	file: function(file,absolute) {
	  /* did we already load this js file? */
	  file = (absolute) ? file : mvc.folders.include + file;
	  if (!mvc.loaded[file]) {
	  	mvc.log('Loading... ' + file + '.js');
		  mvc.request({url: file + '.js', dataType: 'script', cache: true});
		  mvc.loaded[file] = true;
	  }
	},
	
	controller: function(controller,method,noattach) {
		mvc.load.file(mvc.folders.controller + controller + '/' + method,true);
		if (!noattach) {
		  mvc.attach(mvc.config.controller + controller + mvc.config.method + method);
		}
	},
	
	model: function(file) {
		mvc.load.file(mvc.folders.model + file,true);
		var x = mvc[file];
		return jQuery.extend(true,new x(), new mvcModel());
	},
	
	/*
	var output = mvc.load.view('template',data);
	
	Get view template, compile it, and phrase it.
	name = name of the template file to load - also used as the name of the compiled template
	data = phrase into the template
	*/
	view: function(name,data) {
	  // jQuery template stores them in .template[name] so let's see if there have one named?
	  if (!jQuery.template[name]) {
	    // get the template
	    var template = mvc.request({url: mvc.folders.view + name + mvc.views.extension + '.js', dataType: 'html'});
	    template = (typeof(template) === 'string') ? template : ' ';
	    jQuery.template(name,template);
	  }
	
	  // phrase and render the template
	  return jQuery.tmpl(name,data);
	}

};