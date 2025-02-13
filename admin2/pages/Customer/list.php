<?php
    array_push($cssStack,'<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">');
    array_push($cssStack, '<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">');
    array_push($cssStack, '<link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">');


    array_push($jsStack, '<script src="plugins/datatables/jquery.dataTables.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>');
    array_push($jsStack, '<script src="plugins/jszip/jszip.min.js"></script>');
    array_push($jsStack, '<script src="plugins/pdfmake/pdfmake.min.js"></script>');
    array_push($jsStack, '<script src="plugins/pdfmake/vfs_fonts.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>');
    array_push($jsStack, '<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>');


    array_push($jsStack, '
        <script>
            $(function() {
                $("#example1, #example2").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo(\'#example1_wrapper .col-md-6:eq(0)\');
            });

            function confirmDelete() {
                return confirm(\'Are you sure you want to delete this?\');
            }
        </script>
    ');


?>





<div class="card">
    <div class="card-header">
        <h3 class="card-title">Customer list</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="card-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Create At</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
               
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Create At</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>