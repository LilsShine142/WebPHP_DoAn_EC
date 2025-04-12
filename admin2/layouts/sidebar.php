 <aside class="main-sidebar sidebar-dark-primary elevation-4">
   <!-- Brand Logo -->
   <a href="index3.html" class="brand-link">
     <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
     <span class="brand-text font-weight-light">AdminLTE 3</span>
   </a>

   <!-- Sidebar -->
   <div class="sidebar">
     <!-- Sidebar user panel (optional) -->
     <div class="user-panel mt-3 pb-3 mb-3 d-flex">
       <div class="image">
         <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
       </div>
       <div class="info">
         <a href="#" class="d-block">Alexander Pierce</a>
       </div>
     </div>

     <!-- SidebarSearch Form -->
     <div class="form-inline">
       <div class="input-group" data-widget="sidebar-search">
         <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
         <div class="input-group-append">
           <button class="btn btn-sidebar">
             <i class="fas fa-search fa-fw"></i>
           </button>
         </div>
       </div>
     </div>

     <!-- Sidebar Menu -->
     <nav class="mt-2">
       <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
         <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         <li class="nav-item">
           <a href="index.php?page=pages/Statistics/Statistical.php" class="nav-link">
             <i class="nav-icon fas fa-sitemap"></i>
             <p>
               Statistical
             </p>
           </a>
         </li>
         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-sitemap"></i>
             <p>
               Category
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <!-- <li class="nav-item">
               <a href="" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Create</p>
               </a>
             </li> -->
             <li class="nav-item">
               <a href="index.php?page=pages/Category/list.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>List</p>
               </a>
             </li>
           </ul>
         </li>

         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-box"></i>
             <p>
               Product
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <li class="nav-item">
               <a href="index.php?page=pages/Product/create.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Create</p>
               </a>
             </li>
             <li class="nav-item">
               <a href="index.php?page=pages/Product/list.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>List</p>
               </a>
             </li>
             <!-- <li class="nav-item">
               <a href="index.php?page=pages/Product/productVariationsList.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Variations List</p>
               </a>
             </li> -->
           </ul>
         </li>

         <li class="nav-item">
           <a href="index.php?page=pages/Order/list.php" class="nav-link">
             <i class="nav-icon fas fa-users"></i>
             <p>Order</p>
           </a>
         </li>

         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-users"></i>
             <p>
               User
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <li class="nav-item">
               <a href="index.php?page=pages/User/create.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Create</p>
               </a>
             </li>
             <li class="nav-item">
               <a href="index.php?page=pages/User/list.php&type=employee" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Employee</p>
               </a>
             </li>
             <li class="nav-item">
               <a href="index.php?page=pages/User/list.php&type=customer" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Customer</p>
               </a>
             </li>

           </ul>
         </li>
         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-box"></i>
             <p>
               Provider
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <li class="nav-item">
               <a href="index.php?page=pages/Provider/create.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Create</p>
               </a>
             </li>
             <li class="nav-item">
               <a href="index.php?page=pages/Provider/list.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>List</p>
               </a>
             </li>
           </ul>
         </li>
         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-box"></i>
             <p>
               Goode Receipt Note
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <li class="nav-item">
               <a href="index.php?page=pages/GoodReceiptNote/create.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Create</p>
               </a>
             </li>
             <li class="nav-item">
               <a href="index.php?page=pages/GoodReceiptNote/list.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>List</p>
               </a>
             </li>
           </ul>
         </li>
         <li class="nav-item">
           <a href="index.php?page=pages/Permission/role.php" class="nav-link">
             <i class="nav-icon fas fa-user-shield"></i>
             <p>Permission</p>
           </a>
         </li>

         <!-- feedback -->
          <li class="nav-item">
            <a href="index.php?page=pages/Feedback/list.php" class="nav-link">
              <i class="nav-icon fas fa-comments"></i>
              <p>Feedback</p>
            </a>
          </li>

         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-users"></i>
             <p>
               Feature
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <li class="nav-item">
               <a href="" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>Modify</p>
               </a>
             </li>
           </ul>
         </li>
       </ul>
     </nav>
     <!-- /.sidebar-menu -->
   </div>
   <!-- /.sidebar -->
 </aside>