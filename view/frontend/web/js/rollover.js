/**
 * @author    Ethnic
 * @copyright Copyright (c) Ethnic
 * @package   Ethnic_RollOverImage
 */
define(
    [
    'uiComponent',
    'ko'
    ], function (Component, ko) {
        'use strict';

        return Component.extend(
            {
                defaults: {
                    hoverImage: '',
                    animation: true,
                    animationDuration: '0.5',
                    lazyLoad: true
                },

                /**
                 * Initialize the Magento UI Component
                 */
                initialize: function (config, element) {
                    this._super();

                    // Touch device detection - prevent running on mobile
                    var isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);
                    if (isTouchDevice) {
                        return this;
                    }

                    this.element = element;
                    this.originalImage = element.src;

                    this.currentImage = ko.observable(element.src);

                    this.setupLazyLoad();
                    this.applyMagentoBindings();

                    return this;
                },

                /**
                 * Wait until image is scrolled into view before downloading hover image
                 */
                setupLazyLoad: function () {
                    var self = this;

                    var preload = function () {
                        var img = new Image();
                        img.src = self.hoverImage;
                    };

                    if (this.lazyLoad && 'IntersectionObserver' in window) {
                        var observer = new IntersectionObserver(
                            function (entries) {
                                if (entries[0].isIntersecting) {
                                    preload();
                                    observer.disconnect();
                                }
                            }, { rootMargin: "200px" }
                        );

                        observer.observe(this.element);
                    } else {
                        preload();
                    }
                },

                /**
                 * Bind Knockout events directly to the HTML Image tag
                 */
                applyMagentoBindings: function () {
                    var self = this;

                    // Set up CSS transition if animation is enabled in Magento Admin
                    if (this.animation) {
                        this.element.style.transition = 'opacity ' + this.animationDuration + 's ease-in-out';
                    }

                    // Subscribe to the Knockout Observable to handle image swapping
                    this.currentImage.subscribe(
                        function (newSrc) {
                            if (self.animation) {
                                self.element.style.opacity = '0.6';

                                // Wait half the transition time, swap image, fade back in
                                setTimeout(
                                    function () {
                                        self.element.src = newSrc;
                                        self.element.style.opacity = '1';
                                    }, (parseFloat(self.animationDuration) * 1000) / 2
                                );
                            } else {
                                self.element.src = newSrc;
                            }
                        }
                    );

                    // Tie the Knockout Mouse events to our Component methods
                    ko.applyBindingsToNode(
                        this.element, {
                            event: {
                                mouseenter: this.onMouseEnter.bind(this),
                                mouseleave: this.onMouseLeave.bind(this)
                            }
                        }, this
                    );
                },

                /**
                 * Triggered by Knockout mouseenter
                 */
                onMouseEnter: function () {
                    this.currentImage(this.hoverImage);
                },

                /**
                 * Triggered by Knockout mouseleave
                 */
                onMouseLeave: function () {
                    this.currentImage(this.originalImage);
                }
            }
        );
    }
);
