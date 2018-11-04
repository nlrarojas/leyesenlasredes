(function ($, Drupal) {
  $(document).ready(function () {
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
          if (response.response === 1) {            
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; background: #d4edda; " + 
                "color: #155724; border-color: #c3e6cb;;' class=alert alert-dismissible fade show role=alert>" +
                "Ahora sigue el tema de interés." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");   
            setTimeout(function() {
              location.reload();
            }, 3000);
          } else { 
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; color: #856404;" +
                "background-color: #fff3cd; border-color: #ffeeba;'" + 
                "class=alert alert-dismissible fade show role=alert>" +
                "Ya sigue este tema de interés." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");             
          } 
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
          if (response.response === 1) {            
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; background: #d4edda; " + 
                "color: #155724; border-color: #c3e6cb;;' class=alert alert-dismissible fade show role=alert>" +
                "Ahora sigue el proyecto." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");   
            setTimeout(function() {
              location.reload();
            }, 3000);
          } else { 
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; color: #856404;" +
                "background-color: #fff3cd; border-color: #ffeeba;'" + 
                "class=alert alert-dismissible fade show role=alert>" +
                "Ya sigue este proyecto." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");             
          } 
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
          if (response.response === 1) {            
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; background: #d4edda; " + 
                "color: #155724; border-color: #c3e6cb;;' class=alert alert-dismissible fade show role=alert>" +
                "Votación iniciada." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");               
          } else { 
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; color: #856404;" +
                "background-color: #fff3cd; border-color: #ffeeba;'" + 
                "class=alert alert-dismissible fade show role=alert>" +
                "Ya existe una votación abierta." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");             
          } 
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
          if (response.response === 1) {            
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; background: #d4edda; " + 
                "color: #155724; border-color: #c3e6cb;;' class=alert alert-dismissible fade show role=alert>" +
                "Votación finalizada." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");               
          } else { 
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; color: #856404;" +
                "background-color: #fff3cd; border-color: #ffeeba;'" + 
                "class=alert alert-dismissible fade show role=alert>" +
                "No se puede cerrar la votación por qué ya está cerrada o no ha sido abierta." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");             
          } 
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#vote-up", function (event) {
      event.preventDefault();

      const project = $(this).data("project");      
      
      $.ajax({
        url: '/call/ajax/vote-up',
        type: 'POST',
        data: {
          project: project           
        },
        success: function (response) {
          $("#vote-up").html(""+response.votesFavor+"&nbsp;&nbsp;<span class='glyphicon glyphicon-thumbs-up'></span>");
          $("#vote-down").html(""+response.votesNonFavor+"&nbsp;&nbsp;<span class='glyphicon glyphicon-thumbs-down'></span>");
          if (response.response === 1) {            
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; background: #d4edda; " + 
                "color: #155724; border-color: #c3e6cb;;' class=alert alert-dismissible fade show role=alert>" +
                "Su voto ha sido registrado." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");               
          } else { 
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; color: #856404;" +
                "background-color: #fff3cd; border-color: #ffeeba;'" + 
                "class=alert alert-dismissible fade show role=alert>" +
                "Ya ha votado por este proyecto." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");             
          } 
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

    $(document).on("click", "#vote-down", function (event) {
      event.preventDefault();

      const project = $(this).data("project");      
      
      $.ajax({
        url: '/call/ajax/vote-down',
        type: 'POST',
        data: {
          project: project
        },
        success: function (response) {
          $("#vote-up").html(""+response.votesFavor+"&nbsp;&nbsp;<span class='glyphicon glyphicon-thumbs-up'></span>");
          $("#vote-down").html(""+response.votesNonFavor+"&nbsp;&nbsp;<span class='glyphicon glyphicon-thumbs-down'></span>");
          if (response.response === 1) {            
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; background: #d4edda; " + 
                "color: #155724; border-color: #c3e6cb;;' class=alert alert-dismissible fade show role=alert>" +
                "Su voto ha sido registrado." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");               
          } else { 
            $("#navbar").after("<div style='width: 74.6%; margin: 0 auto; color: #856404;" +
                "background-color: #fff3cd; border-color: #ffeeba;'" + 
                "class=alert alert-dismissible fade show role=alert>" +
                "Ya ha votado por este proyecto." + 
                "<button type=button class=close data-dismiss=alert aria-label=Close>" + 
                  "<span aria-hidden=true>&times;</span>" + 
                "</button>" +
              "</div>");             
          } 
        },
        error: function (response) {
          alert('Hubo un error inesperado intentar más tarde');
        }
      });
    });

  });
}(jQuery, Drupal));