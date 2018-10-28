(function ($, Drupal) {
  $(document).ready(function () {
    /*$(".use-ajax.button").on("click", function (event) {
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
    });*/

    $(document).on("click", "#follow-topic", function (event) {
      event.preventDefault();

      const topic = $(this).data("topic");      

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

      const topic = $(this).data("topic");      

      $.ajax({
        url: '/call/ajax/accept-nomination',
        type: 'POST',
        data: {
          carga: topic          
        },
        success: function (response) {
          alert("Dejo de seguir el tema de interes");          
          location.reload();
        },
        error: function (response) {          
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });
  });  
}(jQuery, Drupal));
