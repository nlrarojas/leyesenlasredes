(function ($, Drupal) {
  $(document).ready(function () {
    /*$(".use-ajax.button").on("click", function (event) {
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
      }
    });

    $(document).on("click", "#cancel-confirmation-trucker", function (event) {
      event.preventDefault();
      $('button.close').click();
    });*/

    $(document).on("click", "#follow-topic", function (event) {
      event.preventDefault();

      const topic = $(this).data("project");

      $.ajax({
        url: '/call/ajax/follow-topic',
        type: 'POST',
        data: {
          topic: topic
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
        url: '/call/ajax/unfollow-topic',
        type: 'POST',
        data: {
          topic: topic
        },
        success: function (response) {
          location.reload();
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#follow-project", function (event) {
      event.preventDefault();

      const project = $(this).data("project");
      $.ajax({
        url: '/call/ajax/follow-project',
        type: 'POST',
        data: {
          project: project
        },
        success: function (response) {
          location.reload();
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#unfollow-project", function (event) {
      event.preventDefault();

      const project = $(this).data("project");
      $.ajax({
        url: '/call/ajax/remove-project',
        type: 'POST',
        data: {
          project: project
        },
        success: function (response) {
          location.reload();
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#open-voting", function (event) {
      event.preventDefault();

      const project = $(this).data("project");
      $.ajax({
        url: '/call/ajax/open-voting',
        type: 'POST',
        data: {
          project: project
        },
        success: function (response) {
          location.reload();
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#close-voting", function (event) {
      event.preventDefault();

      const project = $(this).data("project");
      
      $.ajax({
        url: '/call/ajax/close-voting',
        type: 'POST',
        data: {
          project: project
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