<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{!! config('core.name') !!}@yield('title', '')</title>
    @section('meta_tag')
    @show

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
      WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
        </script>

    <!--end::Web font -->

    <!--begin::Global Theme Styles -->
    {{Html::style(module_asset_url('core:assets/metronic-v5/vendors/base/vendors.bundle.css'))}}

    <!--RTL version:<link href="assets/vendors/base/vendors.bundle.rtl.css" rel="stylesheet" type="text/css" />-->
    {{Html::style(module_asset_url('core:assets/metronic-v5/demo/default/base/style.bundle.css'))}}

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    {{-- Page Level Css --}}
    @yield('page_level_css')
    {{Html::style(module_asset_url('core:assets/metronic-v5/vendors/custom/datatables/datatables.bundle.css'))}}
    <!-- END PAGE LEVEL PLUGINS -->

    {{Html::style(module_asset_url('core:resources/views/admin/'.$theme_cms->value.'/css/base.css').'?id='.filemtime(module_asset_path('core:resources/views/admin/'.$theme_cms->value.'/css/base.css')))}}
    
    <link rel="icon" type="image/png" sizes="1024x1024" href="{{asset(!empty($settings->where('name','global')->flatten()->first()->value['favicon']) ? $settings->where('name','global')->flatten()->first()->value['favicon'] : config('app.name'))}}">

    <style type="text/css">
      .loading-overlay{
          top: 0px;
          left: 0px;
          margin-left: 0px;
          margin-top: 0px;
          background-repeat: no-repeat;
          background-position: center;
      }
    </style>

    @yield('page_style_css')


    
     <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
     </script>

  </head>
  <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
    <!-- begin:: Page -->
      <div class="m-grid m-grid--hor m-grid--root m-page">

        <!-- BEGIN: Header -->
        <header id="m_header" class="m-grid__item    m-header d-print-none" m-minimize-offset="200" m-minimize-mobile-offset="200">
          <div class="m-container m-container--fluid m-container--full-height">
            <div class="m-stack m-stack--ver m-stack--desktop">

              <!-- BEGIN: Brand -->
              <div class="m-stack__item m-brand  m-brand--skin-dark ">
                <div class="m-stack m-stack--ver m-stack--general">
                  <div class="m-stack__item m-stack__item--middle m-brand__logo">
                    <a href="index.html" class="m-brand__logo-wrapper">
                      <img src="{{empty($settings->where('name','global')->flatten()->first()->value['logo']) ? module_asset_url('core:assets/images/Spartan.png') : generate_storage_url($settings->where('name','global')->flatten()->first()->value['logo'])}}" alt="logo" class="img-logo"> </a>
                    </a>
                  </div>
                  <div class="m-stack__item m-stack__item--middle m-brand__tools">

                    <!-- BEGIN: Left Aside Minimize Toggle -->
                    <a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block  ">
                      <span></span>
                    </a>

                    <!-- END -->

                    <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                    <a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                      <span></span>
                    </a>

                    <!-- END -->

                    <!-- BEGIN: Topbar Toggler -->
                    <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                      <i class="flaticon-more"></i>
                    </a>

                    <!-- BEGIN: Topbar Toggler -->
                  </div>
                </div>
              </div>

              <!-- END: Brand -->
              <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">


                <!-- BEGIN: Topbar -->
                <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general m-stack--fluid">
                  <div class="m-stack__item m-topbar__nav-wrapper">
                    <ul class="m-topbar__nav m-nav m-nav--inline">
                      <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
                       m-dropdown-toggle="click">
                        <a href="#" class="m-nav__link m-dropdown__toggle">
                          <span class="m-topbar__userpic">
                            <img src="{{module_asset_url('core:assets/images/atomix_user31.png')}}" class="m--img-rounded m--marginless" alt="" />
                          </span>
                          <span class="m-topbar__username m--hide">{{Auth::user()->name}}</span>
                        </a>
                        <div class="m-dropdown__wrapper">
                          <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                          <div class="m-dropdown__inner">
                            <div class="m-dropdown__header m--align-center" style="background: url(assets/app/media/img/misc/user_profile_bg.jpg); background-size: cover;">
                              <div class="m-card-user m-card-user--skin-dark">
                                <div class="m-card-user__pic">
                                  <img src="{{module_asset_url('core:assets/images/atomix_user31.png')}}" class="m--img-rounded m--marginless" alt="" />
                                </div>
                                <div class="m-card-user__details">
                                  <span class="m-card-user__name m--font-weight-500">{{Auth::user()->name}}</span>
                                  <a href="" class="m-card-user__email m--font-weight-300 m-link">{{Auth::user()->email}}</a>
                                </div>
                              </div>
                            </div>
                            <div class="m-dropdown__body">
                              <div class="m-dropdown__content">
                                <ul class="m-nav m-nav--skin-light">
                                  <li class="m-nav__section m--hide">
                                    <span class="m-nav__section-text">Section</span>
                                  </li>
                                  <li class="m-nav__item">
                                    <a href="{{--action('BackEnd\Account@index')--}}" class="m-nav__link">
                                      <i class="m-nav__link-icon flaticon-profile-1"></i>
                                      <span class="m-nav__link-title">
                                        <span class="m-nav__link-wrap">
                                          <span class="m-nav__link-text">My Profile</span>
                                        </span>
                                      </span>
                                    </a>
                                  </li>
                                  <li class="m-nav__separator m-nav__separator--fit">
                                  </li>
                                  <li class="m-nav__item">
                                    <a href="{{action('\App\Http\Controllers\Auth\LoginController@logout')}}" class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">Logout</a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

                <!-- END: Topbar -->
              </div>
            </div>
          </div>
        </header>

        <!-- END: Header -->

        <!-- begin::Body -->
        <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">

          <!-- BEGIN: Left Aside -->
          <button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn"><i class="la la-close"></i></button>
          <div id="m_aside_left" class="m-grid__item  m-aside-left  m-aside-left--skin-dark d-print-none">

            <!-- BEGIN: Aside Menu -->
            <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark " m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">
              <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
                @section('sidebar')
                  {!!$menu!!}
                @show
              </ul>
             </div>

            <!-- END: Aside Menu -->
          </div>

          <!-- END: Left Aside -->
          <div class="m-grid__item m-grid__item--fluid m-wrapper">

            <!-- BEGIN: Subheader -->
            <div class="m-subheader ">
              <div class="d-flex align-items-center">
                <div class="mr-auto">
                  <h3 class="m-subheader__title m-subheader__title--separator">@yield('title_dashboard')</h3>
                  @yield('breadcrumb')
                </div>
              </div>
            </div>

            <!-- END: Subheader -->
            <div class="m-content">

              @php
                  $broadcast = \ Gdevilbat\SpardaCMS\Modules\Core\Entities\Setting::where('name', 'broadcast_message')->first();
              @endphp

              @if(!empty($broadcast) && !empty($broadcast->value))
                <marquee class="d-print-none">
                  <div class="mb-3 text-primary">
                    {!!nl2br($broadcast->value)!!}
                  </div>
                </marquee>
              @endif

              <!--Begin::Section-->
              @yield('content')

              <!--End::Section-->
            </div>
          </div>
        </div>

          {{-- Modal Delete Products --}}
          <div class="modal fade" id="small" tabindex="-1" role="dialog" aria-hidden="true"  aria-labelledby="exampleModalLabel">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Delete Confirmation</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="modal-body"> 
                          <h5 class="text-center">
                              Are You Sure ?
                          </h5>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-primary confirmed">Delete</button>
                          <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cancel</button>
                      </div>
                  </div>
                  <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
          </div>
          {{-- End of Modal Delete --}}

        <!-- end:: Body -->

        <div class="loading-overlay h-100 w-100"></div>

        <!-- begin::Footer -->
        <footer class="m-grid__item   m-footer d-print-none">
          <div class="m-container m-container--fluid m-container--full-height m-page__container">
            <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
              <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
                <span class="m-footer__copyright">
                   &copy; 2019
                </span>
              </div>
            </div>
          </div>
        </footer>

        <!-- end::Footer -->
      </div>

      <!-- end:: Page -->

      <!-- begin::Scroll Top -->
      <div id="m_scroll_top" class="m-scroll-top">
        <i class="la la-arrow-up"></i>
      </div>

      <!-- end::Scroll Top -->

    {{-- Javascript Core --}}
    <script type="text/javascript">
      var base = <?= "'".url('/')."'" ?>;
      var env = "<?=  env('APP_ENV') ?>";
      var disk = "{{config('filesystems.default')}}";
      var storage_url = "{{config('filesystems.disks.'.config('filesystems.default').'.url')}}"
    </script>

    <!--begin::Global Theme Bundle -->
    {{Html::script(module_asset_url('core:assets/metronic-v5/vendors/base/vendors.bundle.js'))}}
    {{Html::script(module_asset_url('core:assets/metronic-v5/demo/default/base/scripts.bundle.js'))}}

    <!--end::Global Theme Bundle -->

    @yield('page_level_js')
    {{Html::script(module_asset_url('core:assets/metronic-v5/vendors/custom/datatables/datatables.bundle.js'))}}

    <!--end::Page Scripts -->

    {{Html::script(module_asset_url('core:resources/views/admin/'.$theme_cms->value.'/js/base.js').'?id='.filemtime(module_asset_path('core:resources/views/admin/'.$theme_cms->value.'/js/base.js')))}}
    @yield('page_script_js')
  </body>
</html>