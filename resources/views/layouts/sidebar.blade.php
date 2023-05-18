 <div class="side-menu-fixed">
     <div class="scrollbar side-menu-bg">
         <ul class="nav navbar-nav side-menu" id="sidebarnav">
             <!-- menu item Dashboard-->
             <li>
                 <a href="{{ route('dashboard') }}">
                     <div class="pull-left"><i class="ti-home"></i><span class="right-nav-text">الرئيسيه</span></div>
                 </a>

             </li>
             <!-- menu title -->
             <li class="pl-4 mt-10 mb-10 font-medium text-muted menu-title">العناصر</li>
             <!-- menu item Elements-->
             <li>
                 <a href="{{ route('category.index') }}">
                     <div class="pull-left"><i class="ti-palette"></i><span class="right-nav-text">الاقسام</span></div>
                 </a>

             </li>
             <!-- menu item calendar-->
             <li>
                 <a href="javascript:void(0);" data-toggle="collapse" data-target="#slider-menu">
                     <div class="pull-left"><i class="ti-slider"></i><span class="right-nav-text">المنتجات</span>
                     </div>
                     <div class="pull-right"><i class="ti-plus"></i></div>
                     <div class="clearfix"></div>
                 </a>
                 <ul id="slider-menu" class="collapse" data-parent="#sidebarnav">
                     <li> <a href="{{ route('product.index') }}">عرض المنتجات</a> </li>
                     <li> <a href="{{ route('product.create') }}">اضافة منتج</a> </li>

                 </ul>
             </li>
             <li>
                 <a href="javascript:void(0);" data-toggle="collapse" data-target="#calendar-menu">
                     <div class="pull-left"><i class="ti-calendar"></i><span class="right-nav-text">السلايدر</span>
                     </div>
                     <div class="pull-right"><i class="ti-plus"></i></div>
                     <div class="clearfix"></div>
                 </a>
                 <ul id="calendar-menu" class="collapse" data-parent="#sidebarnav">
                     <li> <a href="{{ route('slider.index') }}">عرض السلايدر</a> </li>
                     <li> <a href="{{ route('slider.create') }}">اضافة سلايدر</a> </li>
                 </ul>
             </li>
         </ul>
     </div>
 </div>
