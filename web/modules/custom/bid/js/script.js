(function ($, Drupal) {
  $(document).ready(function () {
    $(".use-ajax.button").on("click", function (event) {
      event.preventDefault();

      const project = $(this).data("project");
      const operation = $(this).data("operation");      

      if (operation == "follow-topic") {
        var $myDialog = $('<div><form class="form-confirmation-trucker" action="" method="post" id="form-confirmation-trucker" accept-charset="UTF-8">\n' +
          '  <p>' + 'El tema se añadirá a sus temas de interés.</p>\n' +
          '  <button class="button js-form-submit form-submit btn-success btn" data-project="' + project + '" data-operation="' + operation + '" type="button" id="follow-topic" name="op" value="Confirmar">Aceptar</button>\n' +
          '  <button class="button js-form-submit form-submit btn-danger btn icon-before" type="button" id="cancel-confirmation-trucker" name="op" value="Cancelar">\n' +
          '      <span class="icon glyphicon glyphicon-remove" aria-hidden="true"></span>Cancelar\n' +
          '  </button>\n' +
          '</form></div>').appendTo('body');
        Drupal.dialog($myDialog, {
          title: '¿Desea seguir este tema?'
        }).showModal();
      } else if (operation == "unfollow-topic") {
        var $myDialog = $('<div><form class="form-confirmation-trucker" action="" method="post" id="form-confirmation-trucker" accept-charset="UTF-8">\n' +
          '  <p>' + 'El tema se quitará de sus temas de interés.</p>\n' +
          '  <button class="button js-form-submit form-submit btn-success btn" data-project="' + project + '" data-operation="' + operation + '" type="button" id="unfollow-topic" name="op" value="Confirmar">Aceptar</button>\n' +
          '  <button class="button js-form-submit form-submit btn-danger btn icon-before" type="button" id="cancel-confirmation-trucker" name="op" value="Cancelar">\n' +
          '      <span class="icon glyphicon glyphicon-remove" aria-hidden="true"></span>Cancelar\n' +
          '  </button>\n' +
          '</form></div>').appendTo('body');
        Drupal.dialog($myDialog, {
          title: '¿Desea dejar de seguir este tema?'
        }).showModal();
      } else if (operation == "follow-project") {
        var $myDialog = $('<div><form class="form-confirmation-trucker" action="" method="post" id="form-confirmation-trucker" accept-charset="UTF-8">\n' +
          '  <p>' + 'El proyecto se añadirá a sus temas de interés.</p>\n' +
          '  <button class="button js-form-submit form-submit btn-success btn" data-project="' + project + '" data-operation="' + operation + '" type="button" id="follow-project" name="op" value="Confirmar">Aceptar</button>\n' +
          '  <button class="button js-form-submit form-submit btn-danger btn icon-before" type="button" id="cancel-confirmation-trucker" name="op" value="Cancelar">\n' +
          '      <span class="icon glyphicon glyphicon-remove" aria-hidden="true"></span>Cancelar\n' +
          '  </button>\n' +
          '</form></div>').appendTo('body');
        Drupal.dialog($myDialog, {
          title: '¿Desea seguir este proyecto?'
        }).showModal();
      }
    });

    $(document).on("click", "#cancel-confirmation-trucker", function (event) {
      event.preventDefault();
      alert("a");
      $('button.close').click();
    });

    $(document).on("click", "#follow-topic", function (event) {
      event.preventDefault();

      const topic = $(this).data("project");      

      $.ajax({
        url: '/call/ajax/bid',
        type: 'POST',
        data: {
          carga: topic
        },
        success: function (response) {             
          location.reload();
        },
        error: function (response) {          
          alert('Hubo un error inesperado intentar más tarde');          
        }
      });
    });

    $(document).on("click", "#unfollow-topic", function (event) {
      event.preventDefault();

      const topic = $(this).data("project");      

      $.ajax({
        url: '/call/ajax/accept-nomination',
        type: 'POST',
        data: {
          carga: topic          
        },
        success: function (response) {                
          location.reload();
        },
        error: function (response) {          
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });
  });  
}(jQuery, Drupal));



/**
 (function ($, Drupal) {
  $(document).ready(function () {
    $(".use-ajax.button").on("click", function (event) {
      event.preventDefault();

      const name = $(this).data("name");
      const carga = $(this).data("carga");
      const nominado = $(this).data("nominado");
      const empresa = $(this).data("company");
      const precio = $(this).data("precio");

      var $myDialog = $('<div><form class="form-confirmation-trucker" action="" method="post" id="form-confirmation-trucker" accept-charset="UTF-8">\n' +
          '  <p>' + empresa + ' deberá confirmar que puede mover esta carga.</p>\n' +
          '  <button class="button js-form-submit form-submit btn-success btn" data-name="' + name + '" data-carga="' + carga + '" data-nominado="' + nominado + '" type="button" id="submit-confirmation-trucker" name="op" value="Confirmar">Aceptar oferta</button>\n' +
          '  <button class="button js-form-submit form-submit btn-danger btn icon-before" type="button" id="cancel-confirmation-trucker" name="op" value="Cancelar">\n' +
          '      <span class="icon glyphicon glyphicon-remove" aria-hidden="true"></span>Cancelar\n' +
          '  </button>\n' +
          '</form></div>').appendTo('body');
      Drupal.dialog($myDialog, {
        title: '¿Aceptas la oferta de ' + empresa + ' por $' + precio + '?'
      }).showModal();
    });

    $(document).on("click", "#submit-confirmation-trucker", function (event) {
      event.preventDefault();

      const carga = $(this).data("carga");
      const nominado = $(this).data("nominado");

      $.ajax({
        url: '/call/ajax/bid',
        type: 'POST',
        data: {
          carga: carga,
          nominado: nominado
        },
        success: function (response) {
          $('button.close').click();
          location.reload();
        },
        error: function (response) {
          $('button.close').click();
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#cancel-confirmation-trucker", function (event) {
      event.preventDefault();
      $('button.close').click();
    });

    $(".btn-accept-nomination-trucker").on("click", function (event) {
      event.preventDefault();

      const carga = $(this).data("carga");
      const title = $(this).data("question");
      const content = $(this).data("content");

      const $myDialog = $('<div><form class="form-accept-nomination-trucker-popup" action="" method="post" id="form-accept-nomination-trucker-popup" accept-charset="UTF-8">\n' +
          '  <button class="button js-form-submit form-submit btn-danger btn" type="button" data-carga="' + carga + '" id="cancel-accept-nomination-trucker" name="op" value="No">\n' +
          '  No\n' +
          '  </button>\n' +
          '  <button class="button js-form-submit form-submit btn-success btn" type="button" data-carga="' + carga + '" id="submit-accept-nomination-trucker" name="op" value="Si">Si</button>\n' +
          '  <p>'+ content +'</p>'+
          '</form></div>').appendTo('body');
      Drupal.dialog($myDialog, {
        title: '¿Desea confirmar la oferta al embarcador?'
      }).showModal();
    });

    $(document).on("click", "#submit-accept-nomination-trucker", function (event) {
      event.preventDefault();

      const carga = $(this).data("carga");
      const nominado = $(this).data("nominado");

      $.ajax({
        url: '/call/ajax/accept-nomination',
        type: 'POST',
        data: {
          carga: carga,
          nominado: nominado
        },
        success: function (response) {
          $('button.close').click();
          location.reload();
        },
        error: function (response) {
          $('button.close').click();
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#cancel-accept-nomination-trucker", function (event) {
      event.preventDefault();

      const carga = $(this).data("carga");
      const nominado = $(this).data("nominado");

      $.ajax({
        url: '/call/ajax/reject-nomination',
        type: 'POST',
        data: {
          carga: carga,
          nominado: nominado
        },
        success: function (response) {
          $('button.close').click();
          location.reload();
        },
        error: function (response) {
          $('button.close').click();
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });
  });
  if ($(window).width() < 768) {
    $("#content_left").before($("#content_right"));
    $("#content_left").addClass("map_margin");
  } else {
    $("#content_right").before($("#content_left"));
    $("#content_left").removeClass("map_margin");
  }
}(jQuery, Drupal));

 */