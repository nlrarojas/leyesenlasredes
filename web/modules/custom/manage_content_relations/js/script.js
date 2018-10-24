(function ($, Drupal) {
  $(document).ready(function () {/*
    $(".use-ajax.button").on("click", function (event) {
      event.preventDefault();

      const user = $(this).data("user");
      const project = $(this).data("project");
      alert(user);
      alert(project);

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
    });*/

    $(document).on("click", "#followTopic", function (event) {
      event.preventDefault();

      const topic = $(this).data("project");

      $.ajax({
        url: '/call/ajax/reject-nomination',
        type: 'POST',
        data: {
          topic: topic,
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
}(jQuery, Drupal));
