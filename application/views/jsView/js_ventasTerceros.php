 <script type="text/javascript">
$(document).ready(function() {

 

  /*INICIALIZAR MODAL*/
  $('.modal').modal();

 

  $('#tblDetVntTerceros,#tblVerDetVntTerceros').DataTable(
    {
      "info":    false,
      "sort":true,
      //"order": [[ 2, "asc" ]],
      "searching": false,
      "bLengthChange": false,
      "bLengthMenu" : false,
      "lengthMenu": [[10,15,32,100,-1], [10,15,32,100,"Todo"]],
      "language":
        {
          "paginate": {
              "first":      "Primera",
              "last":       "Última ",
              "next":       "Siguiente",
              "previous":   "Anterior"
          },
          "lengthMenu":"Mostrar _MENU_",

          "emptyTable": "No hay datos disponibles en la tabla",
          "search":     "" 
        },
         "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
      
    }
  );

  $('#tblVtsTerceros').DataTable(
    {
      "info":  false,
      "sort": true,
      //"order": [[ 2, "asc" ]],
      "searching": true,
      "bLengthChange": false,
      "bLengthMenu" : false,
      "lengthMenu": [[10,15,32,100,-1], [10,15,32,100,"Todo"]],
      "language":
        {
          "paginate": {
              "first":      "Primera",
              "last":       "Última ",
              "next":       "Siguiente",
              "previous":   "Anterior"
          },
          "lengthMenu":"Mostrar _MENU_",

          "emptyTable": "No hay datos disponibles en la tabla",
          "search":     "" 
        },
        columns: [
                        { 'title': 'FECHA VENTA' },
                        { 'title': 'CODIGO VENTA' },
                        { 'title': 'CLIENTE' },
                        { 'title': 'CONTACTO' },
                        { 'title': 'ESTADO' },
                        { 'title': 'OPCINOES' },

                    ],
         "columnDefs": [
        {"className": "dt-center", "targets": "_all"}
      ],
      "createdRow": 
      function( row, data, dataIndex){
          if( data[4] == "ACTIVA"){
              $(row).css('background-color', 'white');

             
          }
          else {
              $(row).css('background-color', '#d8d8d8');
          }
         

      }
    }
  );

  $('#tblVtsTerceros_filter').hide();
  //Control para el datePicker para buscar por rango de fecha
  inicializaControlFecha();


 // LlenarDTVentasTerceros();
  showNameUser();


});

//FINALIZA DOCUMENT READY





$('#btnFiltroxfechaCLiente').click(function () {
    filtrarxfechaCliente();
  });





function filtrarxfechaCliente(){

  objTable = $("#tblVtsTerceros").DataTable();
  objTable.rows().remove().draw( false ); 
   
  var desde = $("#F1C").val();
  var hasta = $("#F2C").val();

  var form_data = {
      desde : desde,
      hasta: hasta
  };

  $.ajax({
    url: "filtrarxfechaCliente",
    type: "POST",
    data: form_data,
    success: function(res){
      
      var e = JSON.parse(res);

       if(e.results[0].idVnts == null){
       }else{

        var estado;


        for (f=0;f<e.results.length;f++){
          if (e.results[f].activoVnts==1){
            estado =  "ACTIVA";
          }else{
            estado =  "ANULADA";

          }

         if (estado == "ANULADA"){
            objTable.row.add
         ( [
              e.results[f].fechaVnts,
              e.results[f].idVnts,
              e.results[f].clienteVnts,   
              e.results[f].telClteVnts,   
              estado,         
              '<center><a href="#ModalVerDetVnts" id="btnVerDetVnts" class="noHover modal-trigger"><i class="material-icons">&#xE417;</i></a>&nbsp'
          ] ).draw( false )

         }else{
          objTable.row.add
         ( [
              e.results[f].fechaVnts,
              e.results[f].idVnts,
              e.results[f].clienteVnts,   
              e.results[f].telClteVnts,   
              estado,         
              '<center><a href="#ModalVerDetVnts" id="btnVerDetVnts" class="noHover modal-trigger"><i class="material-icons">&#xE417;</i></a>&nbsp'+
              '&nbsp<a href="#!" id="btnAnularVnts" class="Icono noHover"><i class="material-icons">highlight_off</i></a></center>' 
          ] ).draw( false )
         }

        }

      }

    }
  });

}





function showNameUser(){
  var resultado;
   $.ajax({
      url:"NomUsuarioActual",
       async:false,
        success:function(idRegistro){
          var e = JSON.parse(idRegistro);

         // llamado a la funcion que agregar detalles de registros agregando en cada uno el id de ventas al que pertenecen
        
        resultado = e[0].Nombre_visitador;
        $("#nomUser").html(resultado);

        }
    });
}



/*function listandoProductos() {
    $.getJSON("listandoProductos", function(data) {
     $("#productos").append('<option value="NULL"></option>');
    $.each(data, function(i, item) {
     $("#productos").append('<option value="' + item['value'] + '">' +item['desc'] + '</option>');
    });
  });
}*/


$("#productos").autocomplete({
  appendTo: "#result",
  source : function(data, cb){
    console.log(data);
    $.ajax({
      url:"listandoProductos",
      type: "post",
      dataType: "json",
      cache: false,
      async: false,
      data: {data: data.term},
      success: function(res){  


        cb(res);

      }

    });
    
  }
});







//Llenar Data table registro por registro al persiona boton agregar
$("#addProdDet").on('click', function(){

  
  var partesIdProd = $("#productos").val().split(' - ');
  var idProd = partesIdProd[0];
  var descProd =partesIdProd[1];
  var cantProd = $("#txtCantidad").val();
  console.log(partesIdProd+","+idProd+","+descProd+","+cantProd);

  Objtable = $("#tblDetVntTerceros").DataTable();

  if(descProd == "" ||cantProd == ""){
      Materialize.toast("El campo producto o el campo cantidad se encuentran vacio", 4000, 'rounded');
  }else{

     Objtable.row.add
      ( [
          idProd,
          descProd,
          cantProd,
          '<a href="#!" id="RowDelete" class="BtnClose"><i class="material-icons">highlight_off</i></a>'
      ] ).draw( false );
    }
    $("#productos").val("");
   
})

//Accion que desencadena las funciones necesarioas para agregar Maestro y detalle de ventas en la base de datos
$("#addNewVnt").on('click', function(){

  var splitFecha = $("#txtFechaNewFact").val().split('-')
  var fechaVnt = splitFecha[2]+"-"+splitFecha[1]+"-"+splitFecha[0]
  var clienteVnts = $("#txtCLienteNewFact").val();
  var contVnt = $("#txtContactNewFact").val();
  var idRegVnts;

  

   Objtable1 = $("#tblDetVntTerceros").DataTable();

  if(fechaVnt == "" || clienteVnts == ""){
      Materialize.toast("El campo fecha o el campo cliente se encuentran vacio", 4000, 'rounded');
  }else{

    if( ! Objtable1.data().any()){
      Materialize.toast("Agregue productos a la tabla antes de guardar");
    }else{

   

    var regDatGeneralVnt = {
      fechaVnt: fechaVnt,
        clienteVnts: clienteVnts,
        contVnt: contVnt
      };


      $.ajax({
      url: "AgregarNuevaVenta",
       type: "post",
        dataType: "json",
        cache: false,
        async:false,
        data:regDatGeneralVnt,
      success:function(resultado){


        if(resultado==true){
            //Llamado a funcion que busca ultimo id de ventas agregado por usuario, dentro de esta esta la funcion que llama a la funcion que agrega los detalles a la base de datos y agrega el id de ventas al que pertenece cada registro
          BuscarUltimoRegistroVnts();

          Objtable1.rows().remove().draw( false ); 
            $("#tblDetVntTerceros").DataTable();


            LlenarDTVentasTerceros();
        }

      }
    })
    }

  }



})


//Buscar ultimo registro de venta agregado por usuario

function BuscarUltimoRegistroVnts(){
   $.ajax({
      url: "UltimoRegistroVnts",
      async:false,
      success:function(idRegistro){
        var e = JSON.parse(idRegistro);

         // llamado a la funcion que agregar detalles de registros agregando en cada uno el id de ventas al que pertenecen
        AddDetallesVnts(e[0].idVnts);
      }
    })

}


//Guardar Detalle de ventas en BD
function AddDetallesVnts(idRegVnts){

  console.log(idRegVnts);
   Objtable = $("#tblDetVntTerceros").DataTable();
  

    var regDatDetlVnt ={};
    var i = 0;     
    Objtable.rows().data().each( function (index) {
       regDatDetlVnt[i]={};
       
        regDatDetlVnt[i]['idVnts'] = idRegVnts;
        regDatDetlVnt[i]['idArt'] = index[0];
        regDatDetlVnt[i]['descDetArtVnts'] = index[1];
        regDatDetlVnt[i]['cantDetVnts'] = index[2];
        i++;
    });

    console.log(regDatDetlVnt);

    $.ajax({
      url: "AgregarDetalledeVenta",
      type: "post",
      dataType: "json",
      cache: false,
      async:false,
      data:{data:regDatDetlVnt},
      success:function(res){
        console.log(res);

        if(res==true){

           swal({
                    "text":"Venta Agregada",
                    "type":"success",
                    "confirmButtonText":"ACEPTAR",
                    allowOutsideClick:false
                }).then(function(){

                  location.reload();
                    
              });

        }else{
          swal({
                  "text":"Ocurrio un error al eliminar el registro",
                  "type":"error",
                  "confirmButtonText":"ACEPTAR",
                  allowOutsideClick:false
              });
        }

      }

    })


}


//LLenar datatable de ventas al ingresar nueva venta
function LlenarDTVentasTerceros(){
  objTable = $("#tblVtsTerceros").DataTable();

  $.ajax({
    url:"getNewSellingData",
    async:false,
    success:
    function(json){
      
     

       var e = JSON.parse(json);
       console.log(e.results.length);



       if(e.results[0].idVnts == null){
       }else{

        var estado = "ACTIVA";
        for (f=0;f<e.results.length;f++){
          if (e.results[f].activoVnts==1){
            estado =  "ACTIVA";
          }else{
            estado =  "ANULADA";

          }

          if (estado == "ANULADA"){
            objTable.row.add
         ( [
              e.results[f].fechaVnts,
              e.results[f].idVnts,
              e.results[f].clienteVnts,   
              e.results[f].telClteVnts,   
              estado,         
              '<center><a href="#ModalVerDetVnts" id="btnVerDetVnts" class="noHover modal-trigger"><i class="material-icons">&#xE417;</i></a>&nbsp'
          ] ).draw( false )

         }else{
          objTable.row.add
         ( [
              e.results[f].fechaVnts,
              e.results[f].idVnts,
              e.results[f].clienteVnts,   
              e.results[f].telClteVnts,   
              estado,         
              '<center><a href="#ModalVerDetVnts" id="btnVerDetVnts" class="noHover modal-trigger"><i class="material-icons">&#xE417;</i></a>&nbsp'+
              '&nbsp<a href="#!" id="btnAnularVnts" class="Icono noHover"><i class="material-icons">highlight_off</i></a></center>' 
          ] ).draw( false )
         }
        }
      }
    }
  });
}



//Mostrar modal con datos de fila selecionada en encabezado
$("#tblVtsTerceros").delegate("#btnAnularVnts","click", function(){

  var tdItems;
  var idVnt;
   var codVenta;
   var estadoAntes;
   var estado;

  tdItems= $('#tblVtsTerceros').DataTable().rows($(this).parents("tr")).data();
   codVenta =tdItems[0][1]; 
   estadoAntes= tdItems[0][4];


 swal({
          title: '¿Realmente desea anular la transacción?',
          text: "este proceso no se podra revertir",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          cancelButtonText: 'Cancelar',
          confirmButtonText: 'Sí, anular'
        }).then((result) => 
        {
          if (result.value) {

           
             if(estadoAntes == "ACTIVA"){
                estado= "ANULADA";
             }else{
                estado= "ACTIVA";
             }
             console.log(codVenta+", "+estado);

             $.ajax({
              url:"cambiarEstadoVentas/"+codVenta+"/"+estado,
              cache: false,
              async:true
             });
              location.reload();
                   
          }else {
            
          }
        });


  

  })





//Mostrar modal con datos de fila selecionada en encabezado
$("#tblVtsTerceros").delegate("#btnVerDetVnts","click", function(){
 // $('#ModalVerDetVnts').openModal();
  var tdItems;
  var fechaVenta;
   var codVenta;
   var nomCliente;

  $('#tblVerDetVntTerceros').DataTable().rows().remove().draw(false); 

   tdItems= $('#tblVtsTerceros').DataTable().rows($(this).parents("tr")).data();
   fechaVenta =tdItems[0][0]; 
   codVenta =tdItems[0][1]; 
    nomCliente=tdItems[0][2]; 


  $('#txtVerFechaVnt').html(fechaVenta);
    $('#txtVerCodVnt').html(codVenta);  
    $('#txtVerNomClteVnt').html(nomCliente);  
  
  LlenarTablaVerDetVnts(codVenta);

});

function LlenarTablaVerDetVnts(codVenta){

  objTable = $("#tblVerDetVntTerceros").DataTable();

  var codVnts = codVenta;

  $.ajax({
    url:"getSelectedSellingDetailData/"+codVenta,
    cache: false,
    async:true,
    
    success:
    function(json){

      var e = JSON.parse(json);

      for (f=0;f<e.results.length;f++){

       objTable.row.add
       ( [
            e.results[f].idArt,
            e.results[f].descDetArtVnts,
            e.results[f].cantDetVnts
        ] ).draw( false )

      }


    }
    
  });

}


//REMUEVE UNA FILA DE UNA TABLA
$('#tblDetVntTerceros').on('click', '#RowDelete', function(){
  var table = $('#tblDetVntTerceros').DataTable();
  table.row( $(this).parents('tr') ).remove().draw();
    if ( ! table.data().any() ) {
    Materialize.toast("La tabla se encuentra vacia", 4000, 'rounded');
  }



})


$("#txtBuscarTransaccionClientes").on("keyup", function () {
    var table = $("#tblVtsTerceros").DataTable();
    table.search(this.value).draw();
});



 

</script>
