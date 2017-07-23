<script type="text/javascript">


$(document).ready(function() {
//var sel_olt = document.getElementById("olt_num"); 
//var val_olt = sel_olt.options[sel_olt.selectedIndex].value;
  var table = $('#onu_table').DataTable( {
    "ajax": {
      "url" : "inc/ajax.php?ajax_req=get_onu_list&olt_name=<?php echo $olt ?>",
      "dataSrc" : "rows"
    },
    "columns":  [
      {"data" : "iface"},
      {"data" : "model"},
      {"data" : "mac"},
      {"data" : "status"},
      {
        "class"         : 'details-control',
        "orderable"     : false,
        "data"          : null,
        "defaultContent": ''
      }

    ],
    "paging":   true,
    "ordering": true,
    "info":     true
  } );

  // Add event listener for opening and closing details
  $('#onu_table tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      // Open this row

      var res = $.ajax({
                   url  : 'inc/ajax.php?ajax_req=get_onu_info&olt_name=<?php echo $olt ?>&iface='+row.data().iface+'&mac='+row.data().mac,
                   async: false
                }).responseText;

      row.child( res ).show();
      tr.addClass('shown');

    }
  } );
} );
</script>

<div class="onu_table_all">
 <table width="100%" class="display" id="onu_table" cellspacing="0">
  <thead>
    <tr>
      <th>interface</th>
      <th>model</th>
      <th>MAC</th>
      <th>status</th>
      <th>info</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th>interface</th>
      <th>model</th>
      <th>MAC</th>
      <th>status</th>
      <th>info</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
 </table>
</div>
