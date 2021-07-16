<?php
include 'inc/functions.inc.php';

is_logged_usuario();

extract($_POST);
extract($_GET);

$usu = new usuario();
$cod = null;

$options = '<option value="'.$usu->get_usuario('id').'">Lembrete para você</option>';
$options.= '<option value="0">Lembrete para TODOS</option>';
$options.= '<option value="" disabled="yes" style="background-color:grey; color:white;">Lembrete para alguém em especifico</option>';

$criterio = new TCriteria();
$criterio->add(new TFilter('id', '>', 1));
$criterio->add(new TFilter('id', '<>', $usu->get_usuario('id')));
$criterio->add(new TFilter('status', '=', 1));
$criterio->setProperty('order', 'nome');

$sql = new TSqlSelect();
$sql->setEntity('usuario');
$sql->addColumn('id as idUsuario');
$sql->setCriteria($criterio);
$result = $conn->query($sql->getInstruction());

for($i=0; $i<$result->rowCount(); $i++){
  extract($result->fetch(PDO::FETCH_ASSOC));

  $options .= '<option value="'.$idUsuario.'">'.ucwords(strtolower(nome_sobrenome($idUsuario))).' - '.registro(registro($idUsuario, 'usuario', 'id_funcao'),'usuario_funcao','nome').'</option>';
}


$popup_deletar = new popup(null, 'amarelo');
        
$msg = "<span id='evento-msg'></span><span id='evento-msg2'>Deseja mesmo deletar este registro?<br><br>";
$msg.= "<input id='remove-event' type='hidden' value=''>";
$msg.= "<button type='button' onclick='remove_event();' class='btn btn-danger'>Sim</button>";
$msg.= "<button type='button' onclick='".$popup_deletar->get_action_hide()."' class='btn btn-primary pull-right'>Não</button></span>";
$popup_deletar->texto = $msg;
$popup_deletar->display = 'none';

$cod .= $popup_deletar->get_pop();

$cod .= '
<!-- fullCalendar 2.2.5-->
<link rel="stylesheet" href="plugins/AdminLTE-master/plugins/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="plugins/AdminLTE-master/plugins/fullcalendar/fullcalendar.print.css" media="print">

    

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-3">
          
          <!-- the events 
          <div class="box box-solid">
            <div class="box-header with-border">
              <h4 class="box-title">Eventos rápidos</h4>
            </div>
            <div class="box-body">
              
              <div id="external-events">
                <div class="external-event bg-green">Almoçar</div>
                <div class="external-event bg-yellow">Fotografia externa</div>
                <div class="external-event bg-aqua">Fotografia studio</div>
                <div class="external-event bg-light-blue">Seleção de fotos</div>
                <div class="external-event bg-red">Entrega de fotos</div>
                <div class="checkbox">
                  <label for="drop-remove">
                    <input type="checkbox" id="drop-remove">
                    remover depois de usar
                  </label>
                </div>
              </div>
            </div>
          </div>
          -->

          <!-- /. box -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Criar evento</h3>
            </div>
            <div class="box-body">
              <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                <ul class="fc-color-picker" id="color-chooser">
                  <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                  <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                </ul>
              </div>
              <!-- /btn-group -->
              
              <!-- DADOS DO FORMULÁRIO -->
              <input id="data-event" type="text" class="form-control" placeholder="dd/mm/AAAA" value="" autocomplete="off" maxlength="10">
              <input id="send-event" type="hidden" value="'.$usu->get_usuario('id').'">
              <select id="receive-event" class="form-control">
                '.$options.'
              </select>
              <div class="input-group">
                <input id="new-event" type="text" class="form-control" placeholder="Digite aqui o evento">
                <div class="input-group-btn">
                  <button id="add-new-event" type="button" class="btn btn-primary btn-flat">Add</button>
                </div>
                <!-- /btn-group -->
              </div>
              <!-- /input-group -->

              <script type="text/javascript">
                $(function(){                
                  $("#data-event").datepicker({
                    format: "dd/mm/yyyy",
                    autoclose: true,
                    todayHighlight: true
                  });
                  //Datemask dd/mm/yyyy
                  $("#data-event").inputmask("dd/mm/yyyy");
                });
              </script>

                <!-- /DADOS DO FORMULÁRIO -->

            </div>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <!-- THE CALENDAR -->
              <div id="calendar"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  



  ';



$html = new template;
$html->set_html($cod);
$html->get_html('Calendário', 'Verifique seus afazeres');



?>

<!-- fullCalendar 2.2.5 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="plugins/AdminLTE-master/plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="plugins/AdminLTE-master/plugins/fullcalendar/locale/pt-br.js"></script>
<!-- Page specific script -->
<script>
  $(function () {

    /* initialize the external events
     -----------------------------------------------------------------*/
    function ini_events(ele) {
      ele.each(function () {

        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        };

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject);

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex: 1070,
          revert: true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        });

      });
    }

    ini_events($('#external-events div.external-event'));

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();

    $('#calendar').fullCalendar({
      eventLimit: true, // for all non-agenda views
      views: {
        agenda: {
          eventLimit: 1 // adjust to 6 only for agendaWeek/agendaDay
        }
      },
      locale: 'pt-br',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      buttonText: {
        today: 'hoje',
        month: 'mês',
        week: 'semana',
        day: 'dia'
      },
      //Random default events
      events: [
      <?php

        function formataDataAgenda($data){
          $data1 = explode(' ', $data);
          $data2a = explode('-', $data1[0]);
          $data2b = explode(':', $data1[1]);
          $data = array_merge($data2a, $data2b);
          return $data;
        }
        //datas comemorativas

        $sql = new TSqlSelect();
        $sql->setEntity('agenda_datas_comemorativas');
        $sql->addColumn('*');

        $result = $conn->query($sql->getInstruction());

        for($i=0, $codCalend = null; $i<$result->rowCount(); $i++){
          
          if($i>0)
            $codCalend.= ',';

          extract($result->fetch(PDO::FETCH_ASSOC));

          if($feriado=='0'){
            $cor = '#DAA520';
          }else{
            $cor = '#800000';
          }
          
          $codCalend.= '{';
          $codCalend.= "title: '$titulo',";
          $codCalend.= "id: '0',";
          $data = formataDataAgenda(date('Y').'-'.$data.' 00:00:00');
          $codCalend.= "start: new Date(".$data[0].", ".($data[1]-1).", ".$data[2].", ".$data[3].", ".$data[4].", ".$data[5]."),";
          $codCalend.= 'allDay: true,';
          //$codCalend.= 'rendering: "background",';
          $codCalend.= 'editable: false,';
          $codCalend.= "backgroundColor: '$cor',";
          $codCalend.= "borderColor: '$cor'";
          $codCalend.= '}';
        }

        echo $codCalend.',';

        //eventos do usuario

        $criterio = new TCriteria();
        $criterio->add(new TFilter('id_usuario_send', '=', $usu->get_usuario('id')));
        $criterio->add(new TFilter('id_usuario_receive', '=', $usu->get_usuario('id')), TExpression::OR_OPERATOR);
        $criterio->add(new TFilter('id_usuario_receive', '=', 0), TExpression::OR_OPERATOR);
        $criterio->add(new TFilter('data_inicio', '>', date('Y-m-d', strtotime('-60 days'))));

        $sql = new TSqlSelect();
        $sql->setEntity('agenda');
        $sql->addColumn('*');
        $sql->setCriteria($criterio);

        $result = $conn->query($sql->getInstruction());

        for($i=0, $codCalend = null; $i<$result->rowCount(); $i++){
          if($i>0)
            $codCalend.= ',';

          extract($result->fetch(PDO::FETCH_ASSOC));

          $codCalend.= '{';
          $codCalend.= "id: '$id',";
          if($id_usuario_receive!=$id_usuario_send){
            if($id_usuario_send!=$usu->get_usuario('id')){
              $codCalend.= "title: '$texto - Enviado por ".nome_sobrenome($id_usuario_send)."',";    
            }else{
              if($id_usuario_receive==0){
                $codCalend.= "title: '$texto - p/Todos',";    
              }else{                
                $codCalend.= "title: '$texto - p/".nome_sobrenome($id_usuario_receive)."',";    
              }
            }
          }else{
            $codCalend.= "title: '$texto',";
          }
          $data = formataDataAgenda($data_inicio);
          $codCalend.= "start: new Date(".$data[0].", ".($data[1]-1).", ".$data[2].", ".$data[3].", ".$data[4].", ".$data[5]."),";
          if($data_termino!= '0000-00-00 00:00:00'){
            $data = formataDataAgenda($data_termino);
            $codCalend.= "end: new Date(".$data[0].", ".($data[1]-1).", ".$data[2].", ".$data[3].", ".$data[4].", ".$data[5]."),";
          }
          $codCalend.= "backgroundColor: '$cor',";
          $codCalend.= "borderColor: '$cor',";
          $codCalend.= '}';
        }

        echo $codCalend;

      ?>
        
      ],
      editable: true,
      droppable: true, // this allows things to be dropped onto the calendar !!!
      drop: function (date, allDay) { // this function is called when something is dropped
        
        // retrieve the dropped element's stored Event Object
        var originalEventObject = $(this).data('eventObject');

        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);

        // assign it the date that was reported
        copiedEventObject.start = date;
        copiedEventObject.allDay = allDay;
        copiedEventObject.backgroundColor = $(this).css("background-color");
        copiedEventObject.borderColor = $(this).css("border-color");

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

        // is the "remove after drop" checkbox checked?
        if ($('#drop-remove').is(':checked')) {
          // if so, remove the element from the "Draggable Events" list
          $(this).remove();
        }
      },

      eventClick: function(calEvent, jsEvent, view) {

        if(calEvent.id!=0){
          $('#remove-event').val(calEvent.id);
          $('#evento-msg2').show();
          $('#evento-msg').hide();
          $('#evento-msg').html('');
        }else{
          $('#evento-msg2').hide();
          $('#evento-msg').show();
          $('#evento-msg').html(calEvent.title);
          
        }

        <?php echo $popup_deletar->get_action_show(); ?>

      },

      eventDrop: function(event, delta, revertFunc) {
        
        if(event.end!=null){
          data_termino = event.end.format();
        }else{
          data_termino = '0000-00-00T00:00:00';
        }

        $.ajax({
            method: "POST",
            url: "inc/ajax_agenda.inc.php",
            data: { 
              op: "update", 
              id: event.id,
              data_inicio: event.start.format(),
              data_termino: data_termino
            }
          }).done(function( data ) {
              console.log(data);
            });

      },

      eventResize: function(event, delta, revertFunc) {


        $.ajax({
          method: "POST",
          url: "inc/ajax_agenda.inc.php",
          data: { 
            op: "update", 
            id: event.id,
            data_termino: event.end.format()
            }
        }) .done(function( data ) {
              console.log(data);
            });

      },
      dayClick: function(date, allDay, jsEvent, view) {

            var p = date.format();
            var arr = p.split('-');
            p = arr[2] + '/' + arr[1] + "/" + arr[0];
            $('#data-event').val(p);
            //alert('a day has been clicked!');
        }

    });

    /* ADDING EVENTS */
    var currColor = "#3c8dbc"; //Red by default
    //Color chooser button
    var colorChooser = $("#color-chooser-btn");

    $("#color-chooser > li > a").click(function (e) {
      e.preventDefault();
      //Save color
      currColor = $(this).css("color");
      //Add color effect to button
      $('#add-new-event').css({"background-color": currColor, "border-color": currColor});
    });

    $("#add-new-event").click(function (e) {
      e.preventDefault();
      //Get value and make sure it is not null
      var text = $("#new-event").val();
      var data = $("#data-event").val();
      var receive = $("#receive-event").val();
      var send = $("#send-event").val();
      //filtros
      if (text.length == 0){
        alert('Você precisa informar o texto do evento.');
        return;
      }
      if(data.length == 0){
        alert('Você precisa informar a data do evento.');
        return;
      }else{
        data = data.split('/');
      }
      var text2 = '';
      if(send != receive){
        if(receive == 0){
          text2 += ' - TODOS';
          create_events();
        }else{
          $.ajax({
            method: "POST",
            url: "inc/ajax_agenda.inc.php",
            data: { op: "usu", usuario: receive }
          })
            .done(function( data ) {
              text2 += ' - p/' + data;
              create_events();
            });
          
        }
      }else{
        create_events();
      }
      
      function create_events(){
        //Create events
        var event = $("<div />");
        event.css({"background-color": currColor, "border-color": currColor, "color": "#fff"}).addClass("external-event");
        event.html(text+text2);
        //$('#external-events').prepend(event);
        //Add draggable funtionality
        //ini_events(event);

        // retrieve the dropped element's stored Event Object
        var originalEventObject = event;
        // we need to copy it, so that multiple events don't have a reference to the same object
        var copiedEventObject = $.extend({}, originalEventObject);
        // assign it the date that was reported
        copiedEventObject.start = new Date(data[2],data[1]-1,data[0], 12, 00);//não sei pq diaxo o mes soma mais um sem ser feito nada, mas ai vai uma gambiarra pra arrumar isso (mes -1)
        //copiedEventObject.allDay = allDay;
        copiedEventObject.title = text+text2;
        copiedEventObject.backgroundColor = currColor;
        copiedEventObject.borderColor = '#fff';

        // render the event on the calendar
        // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);

        $.ajax({
          method: "POST",
          url: "inc/ajax_agenda.inc.php",
          data: { 
            op: "insert", 
            id_usuario_send: send,
            id_usuario_receive: receive,
            texto: text,
            data_inicio: data[2]+'-'+data[1]+'-'+data[0]+' 12:00:00',
            cor: currColor,
            }
        });

      }
    });


  });
  function remove_event(){
    var id_event = $('#remove-event').val();

    $('#calendar').fullCalendar(
      'removeEvents', id_event
    );
    $.ajax({
      method: "POST",
      url: "inc/ajax_agenda.inc.php",
      data: { 
        op: "delete", 
        id: id_event
      }
    });
  }
</script>