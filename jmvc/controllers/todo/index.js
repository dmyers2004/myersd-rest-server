mvc.controller_todo_method_index = {

  __construct: function () {
    m_list = mvc.load.model('m_note');
    m_list.r_get('/sort/id/d');
    $('#list').mvcView('entry',m_list._records);
    mvc.m_note = mvc.load.model('m_note');  
  },

  edfadd: {
    keyup: function() {
      if (mvc.triggerObject.which === 13) {
        mvc.controller_todo_method_index._addnew();
      }    
    }
  },
  
  btnadd: {
    click: function() {
      mvc.controller_todo_method_index._addnew();
    }
  },

  btncomplete: {
    click: function() {
      $(mvc.trigger, 'i').removeClass('icon-star-empty').addClass('icon-star').parent().fadeOut();
      mvc.m_note.id = $(mvc.trigger).data('id');
      mvc.m_note.r_delete();
    }
  },
  
  txttask: {
    click: function() {
      if (mvc.m_note.id !== $(mvc.trigger).data('id')) {
        if ($('.inlineedit').length > 0) {
          mvc.controller_todo_method_index._savecurrent();
        }
        if ($('.inlineedit').length === 0) {
          mvc.m_note.id = $(mvc.trigger).data('id');
          mvc.m_note.title = $(mvc.trigger).text();
          $(mvc.trigger).mvcView('editfield',mvc.m_note);
        }
      }
    },
    
    keyup: function() {
      if (mvc.triggerObject.which === 13) {
        mvc.controller_todo_method_index._savecurrent();
        mvc.m_note.id = undefined;
      }
    }
  },

  _savecurrent: function() {
    /* save the changes */
    var edf = $('.txttask[data-id="'+mvc.m_note.id+'"]').find('input');
    if (edf.val() === '') {
      edf.addClass('error');
    } else {
      mvc.m_note.title = edf.val();
      mvc.m_note.r_put();
      $(edf).parent().html(mvc.m_note.title);
    }
  },
  
  _addnew: function() {
    if ($('#edfadd').val() === '') {
      $('#edfadd').addClass('error');
    } else {
      mvc.m_note.id = '';
      mvc.m_note.title = $('#edfadd').val();
      mvc.m_note.r_post();
      $('#edfadd').removeClass('error').val('');
console.log(mvc.m_note);      
      $('#list').prepend(mvc.load.view('entry',mvc.m_note));
    }
  }

};
