<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Arden :: Creative Agency - A Modern Multipurpose Bootstrap4 Template</title>

    <!--== Favicon ==-->
    <link rel="shortcut icon" href="{{ asset('themes/theme1/img/favicon.ico') }}" type="image/x-icon" />

    <!--== Google Fonts ==-->
    <link href="https://fonts.googleapis.com/css?family=Karla:400,400i,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i" rel="stylesheet">

    <!-- build:css assets/css/app.min.css -->
    <!--== Demo Panel Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/demo-panel.min.css') }}" rel="stylesheet" />
    <!--== Timeline Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/timeline.min.css') }}" rel="stylesheet" />
    <!--== Slicknav Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/slicknav.min.css') }}" rel="stylesheet" />
    <!--== Revolution Slider Settings CSS ==-->
    <link href="{{ asset('themes/theme1/css/settings.css') }}" rel="stylesheet" />
    <!--== Nice Select Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/nice-select.min.css') }}" rel="stylesheet" />
    <!--== Lightslider Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/lightslider.min.css') }}" rel="stylesheet" />
    <!--== Magnific Popup Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/magnific-popup.min.css') }}" rel="stylesheet" />
    <!--== Leaflet Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/leaflet.min.css') }}" rel="stylesheet" />
    <!--== justifiedGallery Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/justifiedGallery.min.css') }}" rel="stylesheet" />
    <!--== jQuery UI Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/jquery-ui.min.css') }}" rel="stylesheet" />
    <!--== Multi Scroll Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/jquery.multiscroll.min.css') }}" rel="stylesheet" />
    <!--== Custom Scrollbar Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/jquery.mCustomScrollbar.min.css') }}" rel="stylesheet" />
    <!--== Fullpage Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/jquery.fullpage.min.css') }}" rel="stylesheet" />
    <!--== Helper Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/helper.min.css') }}" rel="stylesheet" />
    <!--== Animate Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/animate.min.css') }}" rel="stylesheet" />
    <!--== Linea Icon Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/linea.min.css') }}" rel="stylesheet" />
    <!--== Pe Icon 7 Stroke Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/pe-icon-7-stroke.min.css') }}" rel="stylesheet" />
    <!--== Font-Awesome Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/font-awesome.min.css') }}" rel="stylesheet" />
    <!--== Bootstrap Min CSS ==-->
    <link href="{{ asset('themes/theme1/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!--== Main Style CSS ==-->
    <link href="{{ asset('themes/theme1/css/style.css') }}" rel="stylesheet" />
    <!-- endbuild -->

    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="preloader-active">
    {{-- @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif --}}
    <!--== Start Preloader Content ==-->
    <div class="preloader-wrap">
        <div class="preloader">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!--== End Preloader Content ==-->


    <!--== Start Header Wrapper ==-->
    <header class="header-area  transparent sticky-header">
        <div class="container-fluid">
            <div class="row no-gutters align-items-center">
                <div class="col-5 col-lg-2">
                    <div class="header-logo-area">
                        <a href="#">
                            <img class="logo-main" src="{{ asset('assets/images/brand/logo.png') }}" alt="Logo" />
                            <img class="logo-light" src="{{ asset('assets/images/brand/logo.png') }}" alt="Logo" />
                        </a>
                    </div>
                </div>
                <div class="col-lg-8 d-none d-lg-block position-relative">
                    <div class="header-navigation-area">
                        <ul class="main-menu nav justify-content-center">
                            <li class="has-submenu full-width active"><a href="#">Home</a>
                                <ul class="submenu-nav submenu-nav-mega">
                                    <li class="mega-menu-item"><a href="#">Home Sample 01</a>
                                        <ul>
                                            <li class="new">
                                                <a href="#">Freelancer</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/freelancer.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="new">
                                                <a href="#">Christmas</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/christmas.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="new">
                                                <a href="#">Barber</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/barber.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="new">
                                                <a href="#">Classic Agency</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/classic-agency.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Modern Agency</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/modern-agency.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Creative Agency</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/creative-agency.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Presentation</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/presentation.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Business Classic</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/business-classic.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Business Modern</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/business-modern.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Personal Portfolio</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/personal-portfolio.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mega-menu-item"><a href="#">Home Sample 02</a>
                                        <ul>
                                            <li>
                                                <a href="#">Graphic Design</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/graphic-design.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Blog Fullscreen Slider</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/blog-full-slider.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Portfolio Fullscreen Carousel</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/port-full-carousel.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Design studio</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/design-studio.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Minimalism</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/minimal.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Portfolio 01</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/portfolio-1.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Portfolio 02</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/portfolio-2.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Fullscreen Portfolio Slider</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/port-full-slider.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Modern Shop</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/modern-shop.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Blog Masonry Home</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/blog-masonry.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mega-menu-item"><a href="#">Home Sample 03</a>
                                        <ul>
                                            <li>
                                                <a href="#">App Landing Homepage</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/landing-page.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Onepage</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/onepage.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Justified Gallery</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/justified-gallery.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Split Portfolio</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/portfolio-split.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">VCard</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/vcard.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Startup</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/startup.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Marketing Agency</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/marketing-agency.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Metro Portfolio</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/metro-portfolio.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Left menu multipurpose</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/leftmenumultipurpose.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Interior</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/interior.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="mega-menu-item"><a href="#">Home Sample 04</a>
                                        <ul>
                                            <li class="feature">
                                                <a href="#">Business Services</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/business-service.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Architecture</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/architecture.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Restaurant</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/restaurant.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li>
                                                <a href="#">Photo Slider</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/photoslider.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Classic Shop</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/classic-shop.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Events</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/event.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Construction</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/construction.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Finance Business</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/finance-business.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                            <li class="feature">
                                                <a href="#">Wedding</a>
                                                <img src="{{ asset('themes/theme1/img/menu-thumb/wedding.jpg') }}" alt="arden" class="menu-thumb" />
                                            </li>
                                        </ul>
                                    </li>                                    
                                </ul>
                            </li>
                            <li class="has-submenu"><a href="#">Pages</a>
                                <ul class="submenu-nav">
                                    <li><a href="#">About Us</a></li>
                                    <li><a href="#">About Us 2</a></li>
                                    <li><a href="#">About Me</a></li>
                                    <li><a href="#">Contact Us</a></li>
                                    <li><a href="#">Contact 2</a></li>
                                    <li><a href="#">Our Services</a></li>
                                    <li><a href="#">Our Services 2</a></li>
                                    <li><a href="#">Pricing Packages</a></li>
                                    <li><a href="#">Our Team</a></li>
                                    <li><a href="#">404 Page</a></li>
                                    <li><a href="#">Maintenance</a></li>
                                    <li><a href="#">Coming Soon</a></li>
                                    <li><a href="#">F.A.Qs</a></li>
                                </ul>
                            </li>
                            <li class="has-submenu full-width"><a href="#">Elements</a>
                                <ul class="submenu-nav submenu-nav-mega">
                                    <li class="mega-menu-item menu-content" id="d">
                                        <div class="menu-content-inner">
                                            <h4>Hand-crafted elements for <span>multi purposes</span></h4>
                                            <a href="#" target="_blank" class="btn-brand">Purchase</a>
                                        </div>
                                    </li>

                                    <li class="mega-menu-item"><a href="#">Elements 1</a>
                                        <ul>
                                            <li><a href="#">Accordion & Toggles</a></li>
                                            <li><a href="#">Button</a></li>
                                            <li><a href="#">Carousels</a></li>
                                            <li><a href="#">Progress Bar</a></li>
                                            <li><a href="#">Contact forms</a></li>
                                            <li><a href="#">Countdown clock</a></li>
                                            <li><a href="#">Tabs</a></li>
                                            <li><a href="#">Team member</a></li>
                                        </ul>
                                    </li>

                                    <li class="mega-menu-item"><a href="#">Elements 2</a>
                                        <ul>
                                            <li><a href="#">Counter</a></li>
                                            <li><a href="#">Dividers</a></li>
                                            <li><a href="#">Map</a></li>
                                            <li><a href="#">Icon box</a></li>
                                            <li><a href="#">Image Slider</a></li>
                                            <li><a href="#">Lightbox gallery</a></li>
                                            <li><a href="#">Testimonials</a></li>
                                            <li><a href="#">Timeline</a></li>
                                        </ul>
                                    </li>

                                    <li class="mega-menu-item"><a href="#">Elements 3</a>
                                        <ul>
                                            <li><a href="#">Lists</a></li>
                                            <li><a href="#">Media Feed</a></li>
                                            <li><a href="#">Popup video</a></li>
                                            <li><a href="#">Pricing</a></li>
                                            <li><a href="#">Flip Boxes</a></li>
                                            <li><a href="#">Social media button</a></li>
                                            <li><a href="#">Typography</a></li>
                                            <li><a href="#">Animate Text</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-submenu"><a href="#">Blog</a>
                                <ul class="submenu-nav">
                                    <li><a href="#">Blog Grid</a></li>
                                    <li><a href="#">Blog Carousel</a></li>
                                    <li><a href="#">Blog Large Image</a></li>
                                    <li><a href="#">Blog Grid With Image</a></li>
                                    <li><a href="#">Blog Grid With Sidebar</a></li>
                                    <li><a href="#">Blog Grid Masonry Feature</a></li>
                                    <li class="has-submenu"><a href="#">Single Post Layouts</a>
                                        <ul class="submenu-nav">
                                            <li><a href="#">Blog Details</a></li>
                                            <li><a href="#">Left Sidebar</a></li>
                                            <li><a href="#">No Sidebar</a></li>
                                            <li><a href="#">Link Post</a></li>
                                            <li><a href="#">Quote post</a></li>
                                            <li><a href="#">Youtube Post</a></li>
                                            <li><a href="#">Gallery Post</a></li>
                                            <li><a href="#">Vimeo Post</a></li>
                                            <li><a href="#">Audio Post</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-submenu"><a href="#">Portfolio</a>
                                <ul class="submenu-nav">
                                    <li class="has-submenu"><a href="#">Grid Classic</a>
                                        <ul class="submenu-nav">
                                            <li><a href="#">3 Columns</a></li>
                                            <li><a href="#">3 Columns – No
                                                    Gutter</a></li>
                                            <li><a href="#">4 Columns</a></li>
                                            <li><a href="#">4 Columns – No
                                                    Gutter</a></li>
                                            <li><a href="#">5 Columns</a></li>
                                            <li><a href="#">5 Columns – No
                                                    Gutter</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-submenu"><a href="#">Grid Masonry</a>
                                        <ul class="submenu-nav">
                                            <li><a href="#">3 Columns</a></li>
                                            <li><a href="#">3 Columns – No
                                                    Gutter</a></li>
                                            <li><a href="#">4 Columns</a></li>
                                            <li><a href="#">4 Columns – No
                                                    Gutter</a></li>
                                            <li><a href="#">5 Columns</a></li>
                                            <li><a href="#">5 Columns – No
                                                    Gutter</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-submenu"><a href="#">Carousel</a>
                                        <ul class="submenu-nav">
                                            <li><a href="#">3 Columns</a></li>
                                            <li><a href="#">3 Columns – Small
                                                    Gutter</a></li>
                                            <li><a href="#">4 Columns</a></li>
                                            <li><a href="#">4 Columns – Small
                                                    Gutter</a></li>
                                            <li><a href="#">5 Columns</a></li>
                                            <li><a href="#">5 Columns – Small
                                                    Gutter</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-submenu"><a href="#">Grid Metro</a>
                                        <ul class="submenu-nav">
                                            <li><a href="#">3 Columns</a></li>
                                            <li><a href="#">3 Columns – Small
                                                    Gutter</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Justified Gallery</a></li>
                                    <li><a href="#">Grid With Caption</a></li>
                                    <li class="has-submenu"><a href="#">Portfolio Details</a>
                                        <ul class="submenu-nav">
                                            <li><a href="#">Left description</a></li>
                                            <li><a href="#">Right description</a></li>
                                            <li><a href="#">Image gallery</a></li>
                                            <li><a href="#">Image slider</a></li>
                                            <li><a href="#">Video</a></li>
                                            <li><a href="#">Creative</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-submenu"><a href="#">Shop</a>
                                <ul class="submenu-nav">
                                    <li><a href="#">Shop Left Sidebar</a></li>
                                    <li><a href="#">Shop Right Sidebar</a></li>
                                    <li><a href="#">Single Product</a></li>
                                    <li><a href="#">Checkout</a></li>
                                    <li><a href="#">Cart</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-7 col-lg-2">
                    <div class="header-action-area text-end">
                        <a href="#" class="btn-cart"><span class="icon-ecommerce-bag"></span> <sup class="shop-count">0</sup>
                        </a>
                        <button class="btn-search"><span class="icon-basic-magnifier"></span></button>
                        <button class="btn-menu d-lg-none">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--== End Header Wrapper ==-->


    <main class="site-wrapper site-wrapper-reveal">


        <!--== Start Hero Slider Area ==-->
        <div class="hero-slider-area">
            <div id="rev_slider_16_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container" data-alias="creative-agency" data-source="gallery">
                <!-- START REVOLUTION SLIDER 5.4.7 fullwidth mode -->
                <div id="rev_slider_16_1" class="rev_slider fullwidthabanner" data-version="5.4.7">
                    <ul>
                        <!-- SLIDE  -->
                        <li data-index="rs-42" data-transition="slidingoverlayleft,parallaxtoright" data-slotamount="default,default" data-hideafterloop="0" data-hideslideonmobile="off" data-easein="default,default" data-easeout="default,default" data-masterspeed="default,default" data-thumb="{{ asset('themes/theme1/img/slider/creative-agency/100x50_02creative-01.jpg') }}" data-rotate="0,0" data-saveperformance="off" data-title="Slide">
                            <!-- MAIN IMAGE -->
                            <img src="{{ asset('themes/theme1/img/slider/creative-agency/02creative-01.jpg') }}" alt="Arden" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="15" class="rev-slidebg" data-no-retina>
                            <!-- LAYERS -->

                            <!-- LAYER NR. 1 -->
                            <div class="tp-caption  " id="slide-42-layer-1" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['348','248','420','274']" data-fontsize="['150','120','110','70']" data-lineheight="['130','110','90','60']" data-width="none" data-height="none" data-whitespace="nowrap" data-type="text" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":10,"speed":1340,"frame":"0","from":"x:-50px;opacity:0;","to":"o:1;","ease":"Power4.easeInOut"},{"delay":"wait","speed":310,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]' data-textAlign="['center','center','center','center']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]">
                                creative<br/>
                            agency
                            </div>

                            <!-- LAYER NR. 2 -->
                            <div class="tp-caption  " id="slide-42-layer-3" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['293','203','353','218']" data-fontsize="['20','20','20','14']" data-width="none" data-height="none" data-whitespace="nowrap" data-visibility="['on','on','on','off']" data-type="text" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":1000,"speed":910,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":310,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]' data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]">
                                GROW
                                YOUR BRAND
                            </div>

                            <!-- LAYER NR. 3 -->
                            <div class="tp-caption Button-Outline-Secondary rev-btn " id="slide-42-layer-5" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['671','541','671','500']" data-width="none" data-height="none" data-whitespace="nowrap" data-type="button" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":1290,"speed":840,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"},{"frame":"hover","speed":"0","ease":"Linear.easeNone","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(255,255,255,1);bg:rgb(242,182,54);"}]' data-textAlign="['center','center','center','center']" data-paddingtop="[0,0,0,0]" data-paddingright="[30,30,30,30]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[30,30,30,30]">LEARN MORE
                            </div>
                        </li>
                        <!-- SLIDE  -->
                        <li data-index="rs-43" data-transition="slidingoverlayleft,parallaxtoright" data-slotamount="default,default" data-hideafterloop="0" data-hideslideonmobile="off" data-easein="default,default" data-easeout="default,default" data-masterspeed="default,default" data-thumb="{{ asset('themes/theme1/img/slider/creative-agency/100x50_02creative-02.jpg') }}" data-rotate="0,0" data-saveperformance="off" data-title="Slide" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                            <!-- MAIN IMAGE -->
                            <img src="{{ asset('themes/theme1/img/slider/creative-agency/02creative-02.jpg') }}" alt="Arden" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="15" class="rev-slidebg" data-no-retina>
                            <!-- LAYERS -->

                            <!-- LAYER NR. 4 -->
                            <div class="tp-caption  " id="slide-43-layer-1" data-x="['left','left','left','left']" data-hoffset="['570','380','100','30']" data-y="['top','top','top','top']" data-voffset="['371','257','450','274']" data-fontsize="['90','80','60','40']" data-lineheight="['90','80','60','50']" data-width="none" data-height="none" data-whitespace="nowrap" data-type="text" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":10,"speed":1340,"frame":"0","from":"x:-50px;opacity:0;","to":"o:1;","ease":"Power4.easeInOut"},{"delay":"wait","speed":310,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]' data-textAlign="['left','left','left','left']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]">
                                Modern digital<br/>
                            creative agency.
                            </div>

                            <!-- LAYER NR. 5 -->
                            <div class="tp-caption Button-Outline-Primary rev-btn btn-bordered" id="slide-43-layer-5" data-x="['left','left','left','left']" data-hoffset="['570','380','100','60']" data-y="['top','top','top','top']" data-voffset="['613','466','647','480']" data-width="none" data-height="none" data-whitespace="nowrap" data-type="button" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":1290,"speed":840,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"},{"frame":"hover","speed":"0","ease":"Linear.easeNone","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgb(255,255,255);bg:rgb(24,33,65);"}]' data-textAlign="['center','center','center','center']" data-paddingtop="[0,0,0,0]" data-paddingright="[30,30,30,30]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[30,30,30,30]">LEARN MORE
                            </div>
                        </li>
                        <!-- SLIDE  -->
                        <li data-index="rs-44" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="300" data-thumb="{{ asset('themes/theme1/img/slider/creative-agency/100x50_02creative-03.jpg') }}" data-rotate="0" data-saveperformance="off" data-title="Slide" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                            <!-- MAIN IMAGE -->
                            <img src="{{ asset('themes/theme1/img/slider/creative-agency/02creative-03.jpg') }}" alt="Arden" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="15" class="rev-slidebg" data-no-retina>
                            <!-- LAYERS -->

                            <!-- LAYER NR. 6 -->
                            <div class="tp-caption  " id="slide-44-layer-1" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['320','260','320','290']" data-fontsize="['100','80','70','50']" data-lineheight="['100','80','70','50']" data-width="none" data-height="none" data-whitespace="nowrap" data-type="text" data-responsive_offset="on" data-frames='[{"delay":0,"speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]' data-textAlign="['center','center','center','center']" data-paddingtop="[0,0,0,0]" data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]">
                                ABSOLUTELY<br/>
                            AWESOME
                            </div>

                            <!-- LAYER NR. 7 -->
                            <div class="tp-caption Button-Outline-Secondary rev-btn " id="slide-44-layer-2" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['top','top','top','top']" data-voffset="['584','464','514','434']" data-width="none" data-height="none" data-whitespace="nowrap" data-type="button" data-responsive_offset="on" data-responsive="off" data-frames='[{"delay":540,"speed":710,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"auto:auto;","ease":"Power3.easeInOut"},{"frame":"hover","speed":"0","ease":"Linear.easeNone","to":"o:1;rX:0;rY:0;rZ:0;z:0;","style":"c:rgba(255,255,255,1);bg:rgb(242,182,54);"}]' data-textAlign="['center','center','center','center']" data-paddingtop="[0,0,0,0]" data-paddingright="[30,30,30,30]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[30,30,30,30]">OUR SERVICES
                            </div>
                        </li>
                    </ul>
                    <div class="tp-bannertimer tp-bottom"></div>
                </div>
            </div>
        </div>
        <!--== End Hero Slider Area ==-->


        <!--== Start About Area ==-->
        <section class="about about-creative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto text-center">
                        <div class="about-content">
                            <div class="section-title">
                                <h5>Who are we</h5>
                            </div>
                            <h3>Success is no accident. It is hard work, perseverance, learning, studying, sacrifice and
                                most of all, love of what you are doing or learning to do.</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--== End About Area ==-->


        <!--== Start Portfolio Area ==-->
        <section class="portfolio-wrapper">
            <div class="container-fluid">
                <div class="row mtn-30">
                    <div class="col-sm-6 col-lg-4">
                        <div class="portfolio-item">
                            <div class="portfolio-item__thumb">
                                <a href="portfolio-details.html">
                                    <img src="{{ asset('themes/theme1/img/portfolio/portfolio-01.jpg') }}" alt="Arden-Portfolio" />
                                </a>
                            </div>
                            <div class="portfolio-item__info">
                                <h3 class="title"><a href="portfolio-details.html">Fullscreen Slider</a></h3>
                                <a href="portfolio-details.html" class="category">photo</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="portfolio-item">
                            <div class="portfolio-item__thumb">
                                <a href="portfolio-details.html">
                                    <img src="{{ asset('themes/theme1/img/portfolio/portfolio-02.jpg') }}" alt="Arden-Portfolio" />
                                </a>
                            </div>
                            <div class="portfolio-item__info">
                                <h3 class="title"><a href="portfolio-details.html">Video</a></h3>
                                <a href="portfolio-details.html" class="category">graphic, photo</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="portfolio-item">
                            <div class="portfolio-item__thumb">
                                <a href="portfolio-details.html">
                                    <img src="{{ asset('themes/theme1/img/portfolio/portfolio-03.jpg') }}" alt="Arden-Portfolio" />
                                </a>
                            </div>
                            <div class="portfolio-item__info">
                                <h3 class="title"><a href="portfolio-details.html">Image Slider</a></h3>
                                <a href="portfolio-details.html" class="category">graphic</a>
                            </div>
                        </div>
                    </div>                    
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <div class="portfolio-footer mt-50 mt-sm-30">
                            <a href="portfolio.html" class="btn btn-bottom">See all Works</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--== End Portfolio Area ==-->


        <!--== Start Service Area ==-->
        <div class="service-area sp-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="service-skill-area">
                            <div class="row mtn-30">
                                <div class="col-sm-6">
                                    <div class="skill-item text-center">
                                        <div class="skill-item__progress">
                                            <div class="ht-pie-chart" data-percent="90" data-bar-color="#f2b636" data-size="180"></div>
                                        </div>
                                        <div class="skill-item__txt">
                                            <h2 class="h6">Brand strategy</h2>
                                            <p>We have a solid process for building your brand.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="skill-item text-center">
                                        <div class="skill-item__progress">
                                            <div class="ht-pie-chart" data-percent="60" data-bar-color="#182141" data-size="180"></div>
                                        </div>
                                        <div class="skill-item__txt">
                                            <h2 class="h6">Digital solutions</h2>
                                            <p>Your website is the cornerstone of your market.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 ms-auto">
                        <div class="service-content-wrap mt-md-40 mt-sm-40">
                            <h4>Services</h4>
                            <ul class="ht-list mt-25 mt-sm-15">
                                <li>Branding strategy & identity</li>
                                <li>Marketing campaign & PR</li>
                                <li>Website and app designing</li>
                                <li>Video production & editing</li>
                                <li>User experience designing</li>
                                <li>Content creating & maker</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--== End Service Area ==-->


        <!--== Start Testimonial Area ==-->
        <div class="testimonial-area sm-top-wp">
            <div class="testimonial-header-area bg-img" data-bg="{{ asset('themes/theme1/img/testimonial/creative-testi-bg.jpg') }}">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="section-title">
                                <h5>Our Clients</h5>
                                <h2>What they say about us</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="testimonial-content-area">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- Testimonial Carousel Content -->
                            <div class="testimonial-content">
                                <!-- Start Single Testimonial Item -->
                                <div class="testimonial-item">
                                    <h6 class="client-title">Rex Watson</h6>
                                    <span class="client-designation">Everline, Product Manager</span>
                                    <figure class="client-thumb">
                                        <img src="{{ asset('themes/theme1/img/testimonial/client-01.jpg') }}" alt="Client" />
                                    </figure>
                                    <p>“Amazing fast and reliable customer support! The team of developers are willing to go
                                        mile for customer service! Thanks!”</p>
                                </div>
                                <!-- End Single Testimonial Item -->

                                <!-- Start Single Testimonial Item -->
                                <div class="testimonial-item">
                                    <h6 class="client-title">Blanche Fields</h6>
                                    <span class="client-designation">Nord, Marketing</span>
                                    <figure class="client-thumb">
                                        <img src="{{ asset('themes/theme1/img/testimonial/client-02.jpg') }}" alt="Client" />
                                    </figure>
                                    <p>“Excellent support, fast and very didactic answers. The design meets the
                                        expectations import develops very quickly a website.”</p>
                                </div>
                                <!-- End Single Testimonial Item -->

                                <!-- Start Single Testimonial Item -->
                                <div class="testimonial-item">
                                    <h6 class="client-title">Barney Smith</h6>
                                    <span class="client-designation">Fantuno, PR officer</span>
                                    <figure class="client-thumb">
                                        <img src="{{ asset('themes/theme1/img/testimonial/client-03.jpg') }}" alt="Client" />
                                    </figure>
                                    <p>“Great theme, just what we were looking for. Easy to install, easy to navigate. Well
                                        documented. Really enjoyed the support.”</p>
                                </div>
                                <!-- End Single Testimonial Item -->

                                <!-- Start Single Testimonial Item -->
                                <div class="testimonial-item">
                                    <h6 class="client-title">Dean Casey</h6>
                                    <span class="client-designation">Lemimum, Marketing</span>
                                    <figure class="client-thumb">
                                        <img src="{{ asset('themes/theme1/img/testimonial/client-04.jpg') }}" alt="Client" />
                                    </figure>
                                    <p>“Their customer support was amazing. Their help was quick and gave me very clear
                                        instructions to follow.”</p>
                                </div>
                                <!-- End Single Testimonial Item -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--== End Testimonial Area ==-->


        <!--== Start Brand Logo Area ==-->
        <div class="brand-logo-area">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="brand-logo-content">
                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_01.png') }}" alt="Arden-Logo" /></a>
                            </div>

                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_02.png') }}" alt="Arden-Logo" /></a>
                            </div>

                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_03.png') }}" alt="Arden-Logo" /></a>
                            </div>

                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_04.png') }}" alt="Arden-Logo" /></a>
                            </div>

                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_05.png') }}" alt="Arden-Logo" /></a>
                            </div>

                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_06.png') }}" alt="Arden-Logo" /></a>
                            </div>

                            <div class="brand-logo-item">
                                <a href="#"><img src="{{ asset('themes/theme1/img/brand-logo/brand_01.png') }}" alt="Arden-Logo" /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--== End Brand Logo Area ==-->


        <!--== Start Call to Action Area ==-->
        <div class="call-to-action-wrapper parallax bg-img" data-parallax-speed="0.75" data-bg="{{ asset('themes/theme1/img/call-to/call-to-bg-1.jpg') }}">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="call-to-action-content">
                            <div class="call-to-action-content-inner">
                                <h2>Daily agency life.</h2>
                                <a href="#" class="btn btn-bordered">Explore our culture</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--== End Call to Action Area ==-->


        <!--== Start Blog Post Area ==-->
        <div class="blog-post-area sp-y bg-softWhite">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="section-title">
                            <h5>RANDOM STUFF</h5>
                            <h2>From our blog</h2>
                        </div>
                    </div>
                </div>

                <div class="row mtn-30">
                    <!-- Start Blog Post Item -->
                    <div class="col-md-6 col-lg-4">
                        <div class="post-item matchHeight">
                            <span class="post-day">10</span>
                            <div class="post-thumb" data-bg="{{ asset('themes/theme1/img/blog/blog-01.jpg') }}"></div>
                            <div class="post-info">
                                <a class="post-info__cate" href="blog.html">Business</a>
                                <h4 class="post-info__title"><a href="blog-details.html">Weekend trip</a></h4>
                                <div class="post-info__meta"><a href="#">March 10, 2019</a></div>
                                <div class="post-info__excerpts">
                                    <p>Treat yourself to a night re-living the golden age of the railway with a stay at The
                                        Old
                                        Railway Station in Petworth, West Sussex.</p>
                                </div>
                                <div class="post-info__action">
                                    <a href="blog-details.html" class="btn btn-bottom">Read full Post</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Blog Post Item -->

                    <!-- Start Blog Post Item -->
                    <div class="col-md-6 col-lg-4">
                        <div class="post-item matchHeight">
                            <span class="post-day">26</span>
                            <div class="post-thumb" data-bg="{{ asset('themes/theme1/img/blog/blog-02.jpg') }}"></div>
                            <div class="post-info">
                                <a class="post-info__cate" href="blog.html">Life Style</a>
                                <h4 class="post-info__title"><a href="blog-details.html">Become Who You Say You Always
                                        Will</a>
                                </h4>
                                <div class="post-info__meta"><a href="#">March 26, 2019</a></div>
                                <div class="post-info__excerpts">
                                    <p>If I have a chance to whisper the best advice to a baby and he’ll remember it for the
                                        rest of his life is this: ENJOY YOUR LIFE. Nothing</p>
                                </div>
                                <div class="post-info__action">
                                    <a href="blog-details.html" class="btn btn-bottom">Read full Post</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Blog Post Item -->

                    <!-- Start Blog Post Item -->
                    <div class="col-md-6 col-lg-4">
                        <div class="post-item matchHeight">
                            <span class="post-day">09</span>
                            <div class="post-thumb" data-bg="{{ asset('themes/theme1/img/blog/blog-03.jpg') }}"></div>
                            <div class="post-info">
                                <a class="post-info__cate" href="blog.html">Travel</a>
                                <h4 class="post-info__title"><a href="blog-details.html">Explore Vancouver Mountain</a></h4>
                                <div class="post-info__meta"><a href="#">April 09, 2019</a></div>
                                <div class="post-info__excerpts">
                                    <p>During the summer my wife and I got to go on an amazing road trip in Vancouver
                                        Mountain
                                        with our good friends Samuel and Hildegunn Taipale</p>
                                </div>
                                <div class="post-info__action">
                                    <a href="blog-details.html" class="btn btn-bottom">Read full Post</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Blog Post Item -->
                </div>
            </div>
        </div>
        <!--== End Blog Post Area ==-->


    </main>


    <!--== Start Footer Area Wrapper ==-->
    <footer class="footer-area reveal-footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Start Footer Widget Area -->
                    <div class="footer-widget-area sp-y">
                        <div class="row mtn-35">
                            <div class="col-md-6 col-lg-3">
                                <div class="widget-item">
                                    <div class="about-widget">
                                        <a href="index.html"><img src="{{ asset('themes/theme1/img/logo-light.png') }}" alt="Logo" /></a>
                                        <p>During the summer my wife and I got to go on an amazing road trip in Vancouver
                                            Mountain.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-2 ms-auto">
                                <div class="widget-item">
                                    <h4 class="widget-title">ABOUT US</h4>
                                    <div class="widget-body">
                                        <ul class="widget-list">
                                            <li><a href="about.html">Our company</a></li>
                                            <li><a href="contact.html">Contact us</a></li>
                                            <li><a href="service.html">Our services</a></li>
                                            <li><a href="#">Careers</a></li>
                                            <li><a href="team.html">Our team</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-2 ms-auto">
                                <div class="widget-item">
                                    <h4 class="widget-title">CONNECT</h4>
                                    <div class="widget-body">
                                        <ul class="widget-list">
                                            <li><a href="#">Facebook</a></li>
                                            <li><a href="#">Twitter</a></li>
                                            <li><a href="#">Dribbble</a></li>
                                            <li><a href="#">Instagram</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="widget-item">
                                    <h4 class="widget-title">STUDIO</h4>
                                    <div class="widget-body">
                                        <address>
                                            2005 Stokes Isle Apartment. 896, Washington 10010, USA <br>
                                        https://example.com <br>
                                        (+68) 120034509
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer Widget Area -->

                    <!-- Start Footer Copyright Area -->
                    <div class="footer-copyright-area">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <nav class="footer-menu-wrap">
                                    <ul class="footer-menu nav justify-content-center justify-content-md-start">
                                        <li><a href="blog.html">Our Blog</a></li>
                                        <li><a href="portfolio.html">Latest Projects</a></li>
                                        <li><a href="contact.html">Contact Us</a></li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="col-md-6 text-center text-md-end">
                                <div class="copyright-txt mt-sm-15">
                                    <p>&copy; Arden Studio
                                        <script>
                                            document.write(new Date().getFullYear())
                                        </script>
                                        . Made with <span class="text-danger">&hearts;</span> by
                                        HasThemes
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer Copyright Area -->
                </div>
            </div>
        </div>
    </footer>
    <!--== End Footer Area Wrapper ==-->


    <!-- Scroll Top Button -->
    <button class="btn-scroll-top"><i class="fa fa-angle-up"></i></button>


    <!-- Start Off Canvas Menu Wrapper -->
    <aside class="off-canvas-wrapper">
        <div class="off-canvas-inner">
            <!-- Start Off Canvas Content Wrapper -->
            <div class="off-canvas-content">
                <!-- Off Canvas Header -->
                <div class="off-canvas-header">
                    <div class="logo-area">
                        <a href="index.html"><img src="{{ asset('themes/theme1/img/logo.png') }}" alt="Logo" /></a>
                    </div>
                    <div class="close-action">
                        <button class="btn-close"><i class="pe-7s-close"></i></button>
                    </div>
                </div>

                <div class="off-canvas-item">
                    <!-- Start Mobile Menu Wrapper -->
                    <div class="res-mobile-menu">
                        <!-- Note Content Auto Generate By Jquery From Main Menu -->
                    </div>
                    <!-- End Mobile Menu Wrapper -->
                </div>
            </div>
            <!-- End Off Canvas Content Wrapper -->
        </div>
    </aside>
    <!-- End Off Canvas Menu Wrapper -->


    <!-- Start Search Box Area Wrapper -->
    <div class="search-box-wrapper">
        <div class="search-box-content-inner">
            <div class="search-box-form-wrap">
                <form action="#" method="post">
                    <div class="search-form position-relative">
                        <label for="search-input" class="sr-only">Search</label>
                        <input type="search" id="search-input" required />
                        <input type="submit" value="search" class="sr-only" />
                    </div>
                </form>
                <div class="search-note">
                    <p>Hit enter to search or ESC to close</p>
                </div>
            </div>
        </div>

        <button class="search-close"><i class="pe-7s-close"></i></button>
    </div>
    <!-- End Search Box Area Wrapper -->

    <!--=== Start Quick View Content Wrapper ==-->
    <div class="modal fade" id="quick-view">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="quick-view-content-wrap">
                        <div class="row">
                            <!-- Start Product Thumbnail Area -->
                            <div class="col-md-6">
                                <div class="product-thumb-area mb-0">
                                    <div class="ht-slick-slider dots-style-three prod-thumb-inner" data-slick='{"slidesToShow": 1, "infinite": false, "dots": true}'>
                                        <figure class="port-details-thumb-item">
                                            <img src="{{ asset('themes/theme1/img/products/quick-view-thumb.jpg') }}" alt="product" />
                                        </figure>
                                    </div>
                                </div>
                            </div>
                            <!-- End Product Thumbnail Area -->

                            <!-- Start Product Info Area -->
                            <div class="col-md-6">
                                <div class="product-details-info-content-wrap">
                                    <div class="prod-details-info-content">
                                        <h2 class="h4">Sierra Caldera DeWalt</h2>
                                        <div class="price-group">
                                            <del class="old-price">$99.00</del>
                                            <span class="price">$69.99</span>
                                        </div>

                                        <div class="product-config mt-30">
                                            <!-- Start Color Choose Option -->
                                            <div class="config-item">
                                                <h5 class="config-name">Color: <b>Black</b></h5>
                                                <ul class="config-list nav">
                                                    <li class="active ht-tooltip" data-tippy-content="Black"><img src="{{ asset('themes/theme1/img/color/black.jpg') }}" alt="Black"></li>
                                                    <li class="ht-tooltip" data-tippy-content="Yellow"><img src="{{ asset('themes/theme1/img/color/yellow.jpg') }}" alt="Yellow"></li>
                                                </ul>
                                            </div>
                                            <!-- End Color Choose Option -->

                                            <!-- Start Size Select Option -->
                                            <div class="config-item">
                                                <h5 class="config-name">Size: <b>Large</b></h5>
                                                <ul class="config-list nav">
                                                    <li class="ht-tooltip" data-tippy-content="Small"><span>S</span></li>
                                                    <li class="ht-tooltip" data-tippy-content="Medium"><span>M</span></li>
                                                    <li class="active ht-tooltip" data-tippy-content="Large"><span>L</span>
                                                    </li>
                                                    <li class="ht-tooltip" data-tippy-content="Extra Large"><span>XL</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- End Size Select Option -->
                                        </div>

                                        <div class="quick-product-action mt-30">
                                            <div class="action-top">
                                                <div class="pro-qty mr-4">
                                                    <input type="text" id="quantity" title="Quantity" value="1" />
                                                </div>
                                                <button class="btn btn-bordered">Add to Cart</button>
                                            </div>
                                        </div>

                                        <div class="prod-details-footer mt-30">
                                            <div class="prod-details-footer__item">
                                                <div class="footer-item-left">
                                                    <h5 class="item-head"><i class="fa fa-tags"></i> Categories:</h5>
                                                </div>
                                                <div class="footer-item-right">
                                                    <ul class="cate-list nav">
                                                        <li><a href="#">Design</a></li>
                                                        <li><a href="#">Interior</a></li>
                                                        <li><a href="#">Multi</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="prod-details-footer__item">
                                                <div class="footer-item-left">
                                                    <h5 class="item-head"><i class="fa fa-share-alt"></i> Share:</h5>
                                                </div>

                                                <div class="footer-item-right">
                                                    <div class="share-item">
                                                        <a href="#" class="ht-tooltip" data-tippy-content="Share On Facebook"><i
                                                            class="fa fa-facebook"></i></a>
                                                        <a href="#" class="ht-tooltip" data-tippy-content="Share On Twitter"><i
                                                            class="fa fa-twitter"></i></a>
                                                        <a href="#" class="ht-tooltip" data-tippy-content="Share On Google Plus"><i
                                                            class="fa fa-google-plus"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Product Info Area -->
                        </div>
                    </div>
                </div>

                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
        </div>
    </div>
    <!--=== End Quick View Content Wrapper ==-->

    <!--=======================Javascript============================-->
    <!-- build:js assets/js/app.min.js -->
    <!-- build:js {{ asset('themes/theme1/js/app.min.js') }} -->
    <!--=== Modernizr Min Js ===-->
    <script src="{{ asset('themes/theme1/js/modernizr-3.6.0.min.js') }}"></script>
    <!--=== jQuery Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.min.js') }}"></script>
    <!--=== jQuery Migration Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery-migrate.min.js') }}"></script>
    <!--=== Bootstrap Min Js ===-->
    <script src="{{ asset('themes/theme1/js/bootstrap.min.js') }}"></script>
    <!--=== CounterUp Min Js ===-->
    <script src="{{ asset('themes/theme1/js/counterup.min.js') }}"></script>
    <!--=== ImageLoaded Min Js ===-->
    <script src="{{ asset('themes/theme1/js/imagesloaded.min.js') }}"></script>
    <!--=== Isotope Min Js ===-->
    <script src="{{ asset('themes/theme1/js/isotope.pkgd.min.js') }}"></script>
    <!--=== jQuery AjaxChimp Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.ajaxchimp.min.js') }}"></script>
    <!--=== jQuery Appear Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.appear.js') }}"></script>
    <!--=== jQuery Countdown Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.countdown.min.js') }}"></script>
    <!--=== jQuery EasyPieChart Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.easypiechart.min.js') }}"></script>
    <!--=== Fullapage Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.fullpage.min.js') }}"></script>
    <!--=== JustifiedGallery Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.justifiedGallery.min.js') }}"></script>
    <!--=== Magnific Popup Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.magnific-popup.min.js') }}"></script>
    <!--=== MatchHeight Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.matchHeight-min.js') }}"></script>
    <!--=== mCustomScrollbar Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.mCustomScrollbar.min.js') }}"></script>
    <!--=== multiscroll Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.multiscroll.min.js') }}"></script>
    <!--=== Nice Select Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.nice-select.min.js') }}"></script>
    <!--=== Slicknav Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.slicknav.min.js') }}"></script>
    <!--=== Sticky Sidebar Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.sticky-sidebar.min.js') }}"></script>
    <!--=== jQuery SmoothScroll Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery.smooth-scroll.js') }}"></script>
    <!--=== jquery UI Min Js ===-->
    <script src="{{ asset('themes/theme1/js/jquery-ui.min.js') }}"></script>
    <!--=== Leaflet Min Js ===-->
    <script src="{{ asset('themes/theme1/js/leaflet.min.js') }}"></script>
    <!--=== Light Slider Min Js ===-->
    <script src="{{ asset('themes/theme1/js/lightslider.min.js') }}"></script>
    <!--=== Parallax Min Js ===-->
    <script src="{{ asset('themes/theme1/js/parallax.min.js') }}"></script>
    <!--=== Tippy Min Js ===-->
    <script src="{{ asset('themes/theme1/js/tippy.all.min.js') }}"></script>
    <!--=== Typed Min Js ===-->
    <script src="{{ asset('themes/theme1/js/typed.min.js') }}"></script>
    <!--=== Waypoint Min Js ===-->
    <script src="{{ asset('themes/theme1/js/waypoint.min.js') }}"></script>

    <!--=== Active Js ===-->
    <script src="{{ asset('themes/theme1/js/active.js') }}"></script>

    <!-- REVOLUTION JS FILES -->
    <script src="{{ asset('themes/theme1/js/revslider/jquery.themepunch.tools.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/jquery.themepunch.revolution.min.js') }}"></script>

    <!-- SLIDER REVOLUTION 5.0 EXTENSIONS -->
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.actions.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.carousel.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.kenburn.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.layeranimation.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.migration.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.navigation.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.parallax.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.slideanims.min.js') }}"></script>
    <script src="{{ asset('themes/theme1/js/revslider/extensions/revolution.extension.video.min.js') }}"></script>

    <!--=== REVOLUTION JS ===-->
    <script src="{{ asset('themes/theme1/js/revslider/rev-active.js') }}"></script>


</body>

</html>