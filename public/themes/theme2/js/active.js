/*global jQuery */
(function($) {
    "use strict";

    /*===============================
        ----- JS Index -----

    01. Background Image JS
    02. Nice Select
    03. Off Canvas JS
    04. Responsive SlickNav JS
    05. Search Box JS
    06. Pie Chart JS
    07. MatchHeight Js
    08. Reveal Footer JS
    09. Scroll To Top JS
    10. Ajax Contact Form JS
    11. Counter To Up JS
    12. Progress Bar JS
    13. Newsletter Form JS
    14. Portfolio Filter JS
    15. Tippy Tooltip JS
    16. Magnific Popup JS
    17. Portfolio FullScreen Carousel JS
    18. Animated Typed JS
    19. Contact Map JS
    20. Justified Gallery JS
    21. Multiscroll JS
    22. Countdown JS
    23. Sticky Element JS
    24. Portfolio Details Creative JS
    25. Product Quantity JS
    26. Checkout Page Checkbox Accordion
    27. Demo Panel JS
    ==================================*/

    jQuery(document).ready(function($) {

        /*--------------------------
            01. Background Image JS
        ---------------------------*/
        const bgSelector = $("[data-bg]");
        bgSelector.each(function(index, elem) {
            const element = $(elem),
                bgSource = element.data('bg');
            element.css('background-image', 'url(' + bgSource + ')');
        });

        /*--------------------------
            02. Nice Select
        ---------------------------*/
        $("select").niceSelect();

        /*------------------------
            03. Off Canvas JS
        --------------------------*/
        const canvasWrapper = $(".off-canvas-wrapper");
        $(".btn-menu").on('click', function() {
            canvasWrapper.addClass('active');
            $("body").addClass('fix');
        });

        $(".close-action > .btn-close, .off-canvas-overlay").on('click', function() {
            canvasWrapper.removeClass('active');
            $("body").removeClass('fix');
        });

        /*------------------------------
            04. Responsive Slicknav JS
        --------------------------------*/
        $('.main-menu').slicknav({
            appendTo: '.res-mobile-menu',
            closeOnClick: true,
            removeClasses: true,
            closedSymbol: '<i class="icon-arrows-plus"></i>',
            openedSymbol: '<i class="icon-arrows-minus"></i>'
        });

        /*------------------------
            05. Search Box  JS
         -------------------------*/
        const wrapper = $(".search-box-wrapper");
        $(".btn-search").on('click', function() {
            wrapper.addClass('show').fadeIn();
            $("body").addClass("fix");
            $("#search-input").focus();
        });

        $(".search-close").on('click', function() {
            wrapper.removeClass('show').fadeOut();
            $("body").removeClass("fix");
        });

        // Esc Key Close
        $(document).on('keyup', function(event) {
            if (event.keyCode === 27) {
                wrapper.removeClass('show').fadeOut();
                $("body").removeClass("fix");
            }
        });

        /*------------------------
            06. Pie Chart  JS
         -------------------------*/
        const chartSelector = $(".ht-pie-chart");
        chartSelector.each(function() {
            $(this).appear(function() {
                const $this = $(this),
                    amount = '<span class="skill-percent">' + $this.data('percent') + '%</span>';
                $this.html(amount);
                $this.easyPieChart({
                    trackColor: "#eeeeee",
                    scaleColor: false,
                    lineWidth: 5
                });
            })
        });

        /*------------------------
            07. MatchHeight Js
         -------------------------*/
        $(".matchHeight").matchHeight();

        /*------------------------
          08. Reveal Footer JS
         -------------------------*/
        let revealId = $(".reveal-footer"),
            footerHeight = revealId.outerHeight(),
            windowWidth = $(window).width(),
            windowHeight = $(window).outerHeight(),
            leftFixedHeader = $("header.fixed-left"),
            leftFixedHeaderWidth = leftFixedHeader.innerWidth();

        if (windowWidth > 991 && windowHeight > footerHeight) {
            $(".site-wrapper-reveal").css({
                'margin-bottom': footerHeight + 'px'
            });
        }

        if (windowWidth > 1199 && leftFixedHeader.length > 0) {
            $(".footer-area, .site-wrapper--left-header").css({
                'padding-left': leftFixedHeaderWidth + 'px'
            });
        }

        /*------------------------
          09. Scroll To Top JS
         -------------------------*/
        $(".btn-scroll-top").on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 1500);
        });


        /*------------------------
          Full Page Scroll Js
         -------------------------*/
        const fullPageID = $("#fullpage"),
            header = $(".header-area");
        if (fullPageID.length && windowWidth > 767) {
            fullPageID.fullpage({
                navigation: true,
                navigationPosition: 'left',
                paddingTop: '0px',
                paddingBottom: '0px',
                afterLoad: function() {
                    const activeSection = $('.fp-section.active'),
                        mode = activeSection.data('skin') === 'dark' ? 'sticky sticky-header' : 'sticky-not';

                    header.removeClass('sticky-not sticky sticky-header').addClass(mode);
                    $("#fp-nav").removeClass('light dark').addClass(activeSection.data('skin'));
                }
            });
        }


        /*--------------------------
          10. Ajax Contact Form JS
         ---------------------------*/
        const form = $('.contact-form');
        const formMessages = $('.form-message');

        $(form).submit(function(e) {
            e.preventDefault();
            const formData = form.serialize();
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: formData
            }).done(function(response) {
                // Make sure that the formMessages div has the 'success' class.
                $(formMessages).removeClass('alert alert-danger');
                $(formMessages).addClass('alert alert-success fade show');

                // Set the message text.
                formMessages.html("<button type='button' class='close' data-dismiss='alert'>&times;</button>");
                formMessages.append(response);

                // Clear the form.
                $('#contact-form input,#contact-form textarea').val('');
            }).fail(function(data) {
                // Make sure that the formMessages div has the 'error' class.
                $(formMessages).removeClass('alert alert-success');
                $(formMessages).addClass('alert alert-danger fade show');

                // Set the message text.
                if (data.responseText !== '') {
                    formMessages.html("<button type='button' class='close' data-dismiss='alert'>&times;</button>");
                    formMessages.append(data.responseText);
                } else {
                    $(formMessages).text('Oops! An error occurred and your message could not be sent.');
                }
            });
        });

        /*--------------------------
           11. Counter To Up JS
        ----------------------------*/
        const counterId = $('.counter');
        if (counterId.length) {
            counterId.counterUp({
                delay: 10,
                time: 1000
            });
        }

        /*--------------------------
            12. Progress Bar JS
        --------------------------*/
        const skillsBar = $(".progress-line-bar");
        skillsBar.appear(function() {
            skillsBar.each(function(index, elem) {
                var elementItem = $(elem),
                    skillBarAmount = elementItem.data('percent');
                elementItem.animate({
                    width: skillBarAmount
                }, 800);
                elementItem.closest('.progress-bar-item').find('.percent').text(skillBarAmount);
                elementItem.closest('.progress-bar-item').find('.progress-info').css('width', skillBarAmount);
            });
        });


        /*--------------------------------
            13. Newsletter Form JS
        -----------------------------------*/
        const selector = $("#mc-form"),
            subscribeUrl = selector.attr('action');
        selector.ajaxChimp({
            language: 'en',
            url: subscribeUrl,
            callback: mailChimpResponse
        });

        function mailChimpResponse(resp) {
            if (resp.result === 'success') {
                $('.mailchimp-success').html('' + resp.msg).fadeIn(900);
                $('.mailchimp-error').fadeOut(400);
                $("#mc-form").trigger('reset');
            } else if (resp.result === 'error') {
                $('.mailchimp-error').html('' + resp.msg).fadeIn(900);
            }
        }

        /*--------------------------------
            14. Portfolio Filter JS
        -----------------------------------*/
        const activeId = $(".filter-menu li");
        const filterWrapper = $(".filter-content");
        if (filterWrapper.length > 0) {
            filterWrapper.imagesLoaded(function() {
                filterWrapper.isotope();
                activeId.on('click', function() {
                    let $this = $(this),
                        filterValue = $this.data('filter');

                    $(".filter-content").isotope({
                        filter: filterValue
                    });

                    activeId.removeClass('active');
                    $this.addClass('active');
                });
            });
        }

        /*---------------------------
            15. Tippy Tooltip JS
        ------------------------------*/
        tippy('.ht-tooltip', {
            inertia: true,
            animation: 'shift-away',
            arrow: true
        });

        /*---------------------------
           16. Magnific Popup JS
        ------------------------------*/
        // For Video Popup
        const videopopup = $(".btn-video-popup");
        videopopup.magnificPopup({
            type: 'iframe',
            mainClass: 'ht-mfp zoom-animate',
            removalDelay: 800,
            closeBtnInside: false
        });

        // For Image Gallery Popup
        const imgGallery = $(".image-gallery-popup");
        imgGallery.magnificPopup({
            delegate: '[data-mfp-src]',
            type: 'image',
            closeOnContentClick: false,
            closeBtnInside: false,
            mainClass: 'ht-mfp mfp-with-zoom mfp-img-mobile',
            image: {
                verticalFit: true
            },
            gallery: {
                enabled: true
            },
            zoom: {
                enabled: true,
                duration: 400,
                opener: function(element) {
                    return element.find('img');
                }
            }
        });

        // Custom Gallery on Button Click
        const galleryBtnPopup = $(".btn-gallery-popup");
        galleryBtnPopup.on('click', function(event) {
            event.preventDefault();

            const gallery = $(this).attr('href');

            $(gallery).magnificPopup({
                delegate: '[data-mfp-src]',
                type: 'image',
                closeOnContentClick: false,
                closeBtnInside: false,
                mainClass: 'ht-mfp zoom-animate mfp-img-mobile',
                removalDelay: 800,
                image: {
                    verticalFit: true
                },
                gallery: {
                    enabled: true
                }
            }).magnificPopup('open');
        });


        // For Single Image Popup
        const imgpopup = $(".btn-img-popup");
        imgpopup.magnificPopup({
            type: 'image',
            closeOnContentClick: false,
            closeBtnInside: false,
            mainClass: 'ht-mfp zoom-animate',
            removalDelay: 800
        });


        /*----------------------------------------
           17. Portfolio FullScreen Carousel JS
        -------------------------------------------*/
        function portfolioFullSlider() {
            let itemWidth,
                wWidth = window.innerWidth;
            if (wWidth > 768) {
                itemWidth = wWidth / 3;
            } else if (wWidth > 543) {
                itemWidth = wWidth / 2;
            } else {
                itemWidth = wWidth;
            }
            let portWrapper = $(".port-full-carousel-content"),
                height = window.innerHeight,
                footerWrap = $(".social-icons-footer");

            height -= footerWrap.outerHeight();

            portWrapper.find('.port-full-item').width(itemWidth).height(height);
            portWrapper.height(height);
            portWrapper.mCustomScrollbar({
                theme: 'arden',
                axis: 'x',
                autoExpandScrollbar: true,
                mouseWheel: {
                    preventDefault: true
                },
                scrollButtons: {
                    enable: true,
                    scrollAmount: itemWidth,
                    scrollType: 'stepped'
                },
                advanced: {
                    autoExpandHorizontalScroll: true
                }
            });
        }

        portfolioFullSlider();

        $(window).resize(function() {
            portfolioFullSlider();
        });

        /*-------------------------
          18. Animated Typed JS
        -----------------------------*/
        if ($("#typed").length > 0) {
            const typed = new Typed('#typed', {
                stringsElement: '#typed-strings',
                typeSpeed: 100,
                loop: true
            });
        }

        /*-------------------------
          19. Contact Map JS
        -----------------------------*/
        const map_id = $('#map_content');
        if (map_id.length > 0) {
            const $lat = map_id.data('lat'),
                $lng = map_id.data('lng'),
                $zoom = map_id.data('zoom'),
                $maptitle = map_id.data('maptitle'),
                $mapaddress = map_id.data('mapaddress'),
                mymap = L.map('map_content').setView([$lat, $lng], $zoom);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map',
                maxZoom: 14,
                minZoom: 2,
                id: 'mapbox.streets',
                scrollWheelZoom: false,
                accessToken: 'sk.eyJ1IjoicmFqdWh0IiwiYSI6ImNqdHk5dGdpYzJqM3A0NGxsYmI3NmhnN3EifQ.kNdHkgfVGmSz6XPmmfG02A'
            }).addTo(mymap);

            const marker = L.marker([$lat, $lng]).addTo(mymap);
            mymap.zoomControl.setPosition('bottomright');
            mymap.scrollWheelZoom.disable();
        }

        /*-------------------------
          20. Justified Gallery JS
        -----------------------------*/
        $(".image-gallery-justified").justifiedGallery({
            rowHeight: 300,
            maxRowHeight: 300,
            margins: 5,
            captions: false,
            border: 0
        });

        /*-------------------------
          21. Multiscroll JS
        -----------------------------*/
        const multoscroll = $('#multiscroll');
        if (multoscroll.length > 0) {
            multoscroll.multiscroll({
                css3: true,
                navigation: true,
                loopBottom: true,
                loopTop: true,
            });
        }


        /*-------------------------
          22. Countdown JS
        -----------------------------*/
        $(".ht-countdown").each(function(index, element) {
            const $element = $(element),
                $date = $element.data('date');

            $element.countdown($date, function(event) {
                const $this = $(this).html(event.strftime('' +
                    '<div class="countdown-item"><span class="countdown-item__time">%D</span><span class="countdown-item__label">Days</span></div>' +
                    '<div class="countdown-item"><span class="countdown-item__time">%H</span><span class="countdown-item__label">Hours</span></div>' +
                    '<div class="countdown-item"><span class="countdown-item__time">%M</span><span class="countdown-item__label">Minutes</span></div>' +
                    '<div class="countdown-item"><span class="countdown-item__time">%S</span><span class="countdown-item__label">Seconds</span></div>'));
            });
        });


        /*-------------------------
          23. Sticky Element JS
        -----------------------------*/
        if (windowWidth >= 992) {
            $(".sticky-element").stickySidebar({
                topSpacing: 120,
                bottomSpacing: 60
            });
        }


        /*------------------------------------
          24. Portfolio Details Creative JS
        -------------------------------------*/
        $(".portfolio-details-creative-action").on('click', function() {
            $(".portfolio-details-creative-content").toggleClass('show');
        });

        /*------------------------------------
          25. Product Quantity JS
        -------------------------------------*/
        var proQty = $(".pro-qty");
        proQty.append('<a href="#" class="inc qty-btn">+</a>');
        proQty.append('<a href="#" class= "dec qty-btn">-</a>');
        $('.qty-btn').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var oldValue = $button.parent().find('input').val();
            if ($button.hasClass('inc')) {
                var newVal = parseFloat(oldValue) + 1;
            } else {
                // Don't allow decrementing below zero
                if (oldValue > 0) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 0;
                }
            }
            $button.parent().find('input').val(newVal);
        });

        /*--------------------------------------
          26. Checkout Page Checkbox Accordion
        ----------------------------------------*/
        $("#create_pwd").on("change", function() {
            $(".account-create").slideToggle("100");
        });

        $("#ship_to_different").on("change", function() {
            $(".ship-to-different").slideToggle("100");
        });

        /*--------------------------------------
            27. Demo Panel JS
         ----------------------------------------*/
        $(".btn-demo-panel").on('click', function() {
            $(".demo-panel-wrap").toggleClass('open');
        });

        // Free
        $('.freelancer-res-nav .slicknav_nav a').smoothScroll({
            offset: -50,
            beforeScroll: function() {
                $('.off-canvas-wrapper').removeClass('active');
                $('body').removeClass('fix');
            }
        });


        /*-----------------------------------
            Slider All Activation
         ------------------------------------*/
        // Home Creative Agency Testimonial JS
        $(".testimonial-content").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            adaptiveHeight: true,
            gallery: false,
            pager: false,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            responsive: [{
                    breakpoint: 1300,
                    settings: {
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        item: 2,
                        slideMargin: 15,
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 481,
                    settings: {
                        item: 1,
                        slideMargin: 0,
                        controls: false,
                        pager: true
                    }
                }
            ]
        });

        // Home Landing Page Screenshots JS
        $(".app-screenshots-content").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            auto: true,
            addClass: 'lightArrows-2',
            gallery: false,
            pager: false,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            responsive: [{
                    breakpoint: 1300,
                    settings: {
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        item: 2,
                        slideMargin: 15,
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 481,
                    settings: {
                        item: 1,
                        slideMargin: 0,
                        controls: false,
                        pager: true
                    }
                }
            ]
        });

        // Home Creative Agency Brand Logo JS
        $(".brand-logo-content").lightSlider({
            item: 6,
            slideMargin: 30,
            loop: true,
            controls: false,
            pager: false,
            auto: true,
            responsive: [{
                    breakpoint: 1199,
                    settings: {
                        item: 4
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        item: 3
                    }
                },
                {
                    breakpoint: 450,
                    settings: {
                        item: 2
                    }
                }
            ]
        });

        // Home Modern Agency Portfolio JS
        $(".portfolio-carousel").lightSlider({
            item: 2,
            slideMargin: 30,
            loop: true,
            controls: false,
            pager: true,
            addClass: 'portfolio-carousel-wrap',
            responsive: [{
                breakpoint: 576,
                settings: {
                    item: 1,
                    slideMargin: 0
                }
            }]
        });

        // Home Personal Portfolio JS
        $(".portfolio-carousel-portfolio").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            controls: false,
            pager: true,
            addClass: 'portfolio-carousel-wrap',
            responsive: [{
                    breakpoint: 800,
                    settings: {
                        item: 2,
                        slideMargin: 20
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        item: 1,
                        slideMargin: 0
                    }
                }
            ]
        });

        // Home Landing Page Testimonial JS
        $(".testimonial-content-landing").lightSlider({
            item: 1,
            slideMargin: 0,
            loop: true,
            controls: false,
            pager: true,
            auto: true,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap'
        });

        // Home Landing Page Testimonial JS
        $(".blog-full-slider-content").lightSlider({
            item: 1,
            slideMargin: 0,
            loop: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: true,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap lightArrows-2',
            responsive: [{
                breakpoint: 767,
                settings: {
                    controls: false
                }
            }]
        });

        // Home Portfolio 01 JS
        $(".portfolio-home-content").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            controls: false,
            pager: true,
            auto: true,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap',
            responsive: [{
                    breakpoint: 991,
                    settings: {
                        item: 2,
                        slideMargin: 20
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        item: 1,
                        slideMargin: 0
                    }
                }
            ]
        });

        // Home Portfolio FullScreen Slider
        $(".portfolio-fullscreen-slider").lightSlider({
            item: 1,
            slideMargin: 0,
            loop: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: false,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap lightArrows-2',
            responsive: [{
                breakpoint: 767,
                settings: {
                    controls: false,
                    pager: true
                }
            }]
        });

        // Home Modern Shop Feature Product
        $(".product-modern-shop").lightSlider({
            item: 6,
            slideMargin: 30,
            loop: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: false,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap lightArrows-2',
            responsive: [{
                    breakpoint: 1601,
                    settings: {
                        item: 4
                    }
                },
                {
                    breakpoint: 1000,
                    settings: {
                        item: 3,
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 700,
                    settings: {
                        item: 2,
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        item: 1,
                        slideMargin: 0,
                        controls: false,
                        pager: true
                    }
                }
            ]
        });


        // Home Barber Shop Product
        $(".product-barber-shop").lightSlider({
            item: 4,
            slideMargin: 30,
            loop: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: false,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap lightArrows-2 raju',
            responsive: [{
                    breakpoint: 1599,
                    settings: {
                        item: 3
                    }
                },
                {
                    breakpoint: 800,
                    settings: {
                        item: 2,
                        controls: false,
                        pager: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        item: 1,
                        slideMargin: 0,
                        controls: false,
                        pager: true
                    }
                }
            ]
        });

        // Home OnePage Blog Slider
        $(".onepage-blog-content").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            controls: false,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: true,
            adaptiveHeight: false,
            addClass: 'portfolio-carousel-wrap',
            responsive: [{
                    breakpoint: 1080,
                    settings: {
                        item: 2,
                        slideMargin: 20
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        item: 1,
                        slideMargin: 0
                    }
                }
            ]
        });

        // Home Left Multipurpose Service Slider
        $(".service-slider-content").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: true,
            adaptiveHeight: true,
            addClass: 'portfolio-carousel-wrap lightArrows-2',
            responsive: [{
                    breakpoint: 1080,
                    settings: {
                        item: 2,
                        slideMargin: 20,
                        controls: false
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        item: 1,
                        slideMargin: 0,
                        controls: false
                    }
                }
            ]
        });

        // Blog Carousel Page Slider
        $(".blog-carousel-content").lightSlider({
            item: 3,
            slideMargin: 30,
            loop: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: true,
            adaptiveHeight: false,
            addClass: 'portfolio-carousel-wrap lightArrows-2',
            responsive: [{
                    breakpoint: 1080,
                    settings: {
                        item: 2,
                        slideMargin: 20
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        item: 1,
                        slideMargin: 0
                    }
                }
            ]
        });

        // Related Post Slider
        $(".related-post-content").lightSlider({
            item: 2,
            slideMargin: 30,
            loop: true,
            controls: false,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            pager: true,
            adaptiveHeight: false,
            addClass: 'portfolio-carousel-wrap',
            responsive: [{
                breakpoint: 650,
                settings: {
                    item: 1,
                    slideMargin: 0
                }
            }]
        });

        // Portfolio Carousel Slider
        const portCarouselContent = $(".portfolio-carousel-content");
        portCarouselContent.each(function(index, elem) {
            const element = $(elem),
                items = element.data('columns'),
                gutters = element.data('gutters');

            element.lightSlider({
                item: items,
                slideMargin: gutters,
                loop: true,
                controls: true,
                prevHtml: "<i class='icon-arrows-left'></i>",
                nextHtml: "<i class='icon-arrows-right'></i>",
                pager: true,
                adaptiveHeight: false,
                addClass: 'portfolio-carousel-wrap lightArrows-2',
                responsive: [{
                        breakpoint: 1199,
                        settings: {
                            item: 3,
                            controls: false,
                            slideMargin: 30
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            item: 2,
                            controls: false,
                            slideMargin: 20
                        }
                    },
                    {
                        breakpoint: 575,
                        settings: {
                            item: 1,
                            slideMargin: 0
                        }
                    }
                ]
            });
        });

        // Portfolio Details Thumb Slider JS
        $(".portfolio-details-thumb-slider").lightSlider({
            item: 1,
            loop: true,
            pager: false,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>"
        });

        // Product Details Thumb Slider JS
        $(".product-details-thumb-slider").lightSlider({
            item: 1,
            loop: true,
            pager: true,
            controls: true,
            prevHtml: "<i class='icon-arrows-left'></i>",
            nextHtml: "<i class='icon-arrows-right'></i>",
            addClass: 'portfolio-carousel-wrap'
        });

    }); //End Ready Function

    jQuery(window).on('scroll', function() {
        //Scroll top Hide Show
        if ($(window).scrollTop() >= 400) {
            $('.btn-scroll-top').addClass('show');
        } else {
            $('.btn-scroll-top').removeClass('show');
        }

        /*------------------------
          10. Sticky Header JS
         -------------------------*/
        if ($(window).scrollTop() >= 300) {
            $(".sticky-header").addClass('sticky');
        } else {
            $('.sticky-header').removeClass('sticky');
        }
    }); // End Scroll Function

    jQuery(window).on('load', function() {
        // Masonry Grid
        $(".masonryGrid").isotope();

        // Remove Preloader Active Class
        $('body').removeClass('preloader-active');
    }); // End Load Function
}(jQuery));