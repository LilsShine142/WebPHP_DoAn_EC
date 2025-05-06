 <aside class="main-sidebar sidebar-dark-primary elevation-4">
   <!-- Brand Logo -->
   <a href="index3.html" class="brand-link">
     <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
     <span class="brand-text font-weight-light">GARMIN Shop</span>
   </a>

   <script>
     const rolePermissions = {
       1: ['statistical', 'receipt', 'permission', 'user'], // admin
       3: ['product', 'order', 'category', 'provider', 'feedback'] // staff
     };

     document.addEventListener('DOMContentLoaded', function() {
       const user = JSON.parse(localStorage.getItem('user'));
       const allowedMenus = rolePermissions[user?.role_id] || [];

       document.querySelectorAll('.nav-item').forEach(item => {
         const menuId = item.getAttribute('data-menu');
         if (!menuId || allowedMenus.includes(menuId)) {
           item.style.display = 'block';
         } else {
           item.style.display = 'none';
         }
       });
     });
   </script>

   <!-- Sidebar -->
   <div class="sidebar">
     <!-- Sidebar user panel (optional) -->
     <div class="user-panel mt-3 pb-3 mb-3 d-flex">
       <div class="image">
         <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
       </div>
       <div class="info">
         <a href="#" class="d-block" id="admin_name">Alexander Pierce</a>
       </div>
       <script>
         // Kiểm tra khi trang tải xong
         document.addEventListener('DOMContentLoaded', function() {
           // Lấy thông tin user từ localStorage
           const user = JSON.parse(localStorage.getItem('user'));

           // Lấy phần tử hiển thị tên
           const adminNameElement = document.getElementById('admin_name');

           if (user && user.full_name) {
             // Nếu có user, hiển thị tên
             adminNameElement.textContent = user.full_name;
           } else {
             // Nếu không có user, chuyển hướng về trang login
             adminNameElement.textContent = "Admin";
           }
         });
       </script>
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
         <!-- Thống kê -->
         <li class="nav-item" data-menu="statistical">
           <a href="index.php?page=pages/Statistics/Statistical.php" class="nav-link">
             <i class="nav-icon fas fa-chart-bar"></i> <!-- Biểu đồ thống kê -->
             <p>Statistical</p>
           </a>
         </li>

         <!-- Danh mục -->
         <li class="nav-item" data-menu="category">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-tags"></i> <!-- Icon danh mục -->
             <p>
               Category
               <i class="right fas fa-angle-left"></i>
             </p>
           </a>
           <ul class="nav nav-treeview">
             <li class="nav-item">
               <a href="index.php?page=pages/Category/list.php" class="nav-link">
                 <i class="far fa-circle nav-icon"></i>
                 <p>List</p>
               </a>
             </li>
           </ul>
         </li>

         <!-- Sản phẩm -->
         <li class="nav-item" data-menu="product">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-box-open"></i> <!-- Icon sản phẩm -->
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
           </ul>
         </li>

         <!-- Đơn hàng -->
         <li class="nav-item" data-menu="order">
           <a href="index.php?page=pages/Order/list.php" class="nav-link">
             <i class="nav-icon fas fa-shopping-cart"></i> <!-- Icon giỏ hàng -->
             <p>Order</p>
           </a>
         </li>

         <!-- Người dùng -->
         <li class="nav-item" data-menu="user">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-users-cog"></i> <!-- Icon quản lý người dùng -->
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

         <!-- Nhà cung cấp -->
         <li class="nav-item" data-menu="provider">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-truck"></i> <!-- Icon nhà cung cấp -->
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

         <!-- Phiếu nhập kho -->
         <li class="nav-item" data-menu="receipt">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-clipboard-list"></i> <!-- Icon phiếu nhập -->
             <p>
               Goods Receipt Note
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

         <!-- Phân quyền -->
         <li class="nav-item" data-menu="permission">
           <a href="index.php?page=pages/Permission/role.php" class="nav-link">
             <i class="nav-icon fas fa-key"></i> <!-- Icon khóa phân quyền -->
             <p>Permission</p>
           </a>
         </li>

         <!-- Phản hồi -->
         <li class="nav-item" data-menu="feedback">
           <a href="index.php?page=pages/Feedback/list.php" class="nav-link">
             <i class="nav-icon fas fa-comment-dots"></i> <!-- Icon phản hồi -->
             <p>Feedback</p>
           </a>
         </li>

         <!-- Tính năng -->
         <li class="nav-item">
           <a href="#" class="nav-link">
             <i class="nav-icon fas fa-cogs"></i> <!-- Icon cài đặt tính năng -->
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

         <!-- Đăng xuất -->
         <li class="nav-item">
           <button id="logout" class="nav-link" style="background: none; border: none; padding-right: 36px;">
             <i class="nav-icon fas fa-sign-out-alt"></i>
             <p>Logout</p>
           </button>
         </li>
       </ul>
     </nav>
     <!-- /.sidebar-menu -->
   </div>
   <!-- /.sidebar -->
 </aside>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <!-- logout -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
   $(document).ready(function() {
     $('#logout').click(function(e) {
       e.preventDefault(); // Prevent the default link behavior

       Swal.fire({
         title: 'Are you sure?',
         text: "You won't be able to revert this!",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Yes, logout!'
       }).then((result) => {
         if (result.isConfirmed) {
           e.preventDefault();

           // Xóa thông tin đăng nhập khỏi localStorage
           localStorage.removeItem("user");

           // Chuyển hướng về trang đăng nhập
           window.location.href = "../client/pages/login.php";
         }
       });
     });
   });
 </script>