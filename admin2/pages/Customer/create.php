<form method="post" action="">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Customer create</h3>

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
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" placeholder="Enter your first name" name="firstname" value="" >
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" placeholder="Enter your last name" name="lastname" value="">
                    </div>


                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" placeholder="Enter email" name="email" value="">
            </div>


            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" placeholder="Enter phone" name="phone">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" placeholder="Enter address" name="address">
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>

    <!-- /.card -->
</form>