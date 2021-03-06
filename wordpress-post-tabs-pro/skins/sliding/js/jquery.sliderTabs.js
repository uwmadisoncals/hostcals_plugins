(function (a) {
    a.sliderTabs = function (b, c) {
        var d = this,
            e = {
                autoplay: !1,
                tabArrowWidth: 35,
                classes: {
                    leftTabArrow: "",
                    panel: "",
                    panelActive: "",
                    panelsContainer: "",
                    rightTabArrow: "",
                    tab: "",
                    tabActive: "",
                    tabsList: ""
                },
                defaultTab: 1,
                height: null,
                indicators: !1,
                mousewheel: !0,
                position: "top",
                panelArrows: !1,
                panelArrowsShowOnHover: !1,
                tabs: !0,
                tabHeight: 42,
                tabArrows: !0,
                tabSlideLength: 100,
                tabSlideSpeed: 200,
                transition: "slide",
                transitionEasing: "easeOutCubic",
                transitionSpeed: 500,
		selectEvent: "click",
                width: null
            },
            f = a(b),
            g, h, i, j, k, l, m, n, o, p, q = !1,
            r = !0,
            s, t;
        d.selectedTab = e.defaultTab, d.init = function () {
            s = d.settings = a.extend({}, e, c), f.addClass("ui-slider-tabs"), i = f.children("div").addClass("ui-slider-tab-content").remove(), h = f.children("ul").addClass("ui-slider-tabs-list").remove(), h.children("li").remove().appendTo(h), d.count = h.children("li").length, k = a("<div class='ui-slider-tabs-list-wrapper'>"), j = a("<div class='ui-slider-tabs-list-container'>").append(h).appendTo(k), j.find("li").css("height", s.tabHeight + 2), j.find("li a").css("height", s.tabHeight + 2), m = a("<a href='#' class='ui-slider-left-arrow'><div></div></a>").css({
                width: s.tabArrowWidth,
                height: s.tabHeight + 2
            }).appendTo(j).click(function (a) {
                return d.slideTabs("right", s.tabSlideLength), !1
            }), n = a("<a href='#' class='ui-slider-right-arrow'><div></div></a>").css({
                width: s.tabArrowWidth,
                height: s.tabHeight + 2
            }).appendTo(j).click(function (a) {
                return d.slideTabs("left", s.tabSlideLength), !1
            }), l = a("<div class='ui-slider-tabs-content-container'>").append(i), s.position == "bottom" ? f.append(l).append(k.addClass("bottom")) : f.append(k).append(l), s.width && f.width(parseInt(s.width)), s.height && l.height(parseInt(s.height) - s.tabHeight), s.indicators && d.showIndicators(), d.selectTab(s.defaultTab), d.slideTabs("left", 0), w(), D(), f.delegate(".ui-slider-tabs-list li a", s.selectEvent, function () {
                return !a(this).parent().hasClass("tabactive") && !q && d.selectTab(a(this).parent()), !1
            }), g && g.delegate(".ui-slider-tabs-indicator", "click", function () {
                !a(this).hasClass("tabactive") && !q && d.selectTab(a(this).index() + 1)
            }), a.each(s.classes, function (a, b) {
                switch (a) {
                case "leftTabArrow":
                    m.addClass(b);
                    break;
                case "rightTabArrow":
                    n.addClass(b);
                    break;
                case "panel":
                    i.addClass(b);
                    break;
                case "panelsContainer":
                    l.addClass(b);
                    break;
                case "tab":
                    h.find("li").addClass(b);
                    break;
                case "tabsList":
                    h.addClass(b);
                    break;
                default:
                }
            }), s.panelArrows && B(), s.panelArrowsShowOnHover && (o && o.addClass("showOnHover"), p && p.addClass("showOnHover")), l.resize(B), k.resize(function () {
                C(), D()
            }), setInterval(function () {
                var a = l.children(".tabactive");
                a.outerHeight() > l.outerHeight() && r && A(a)
            }, 100), C(), s.tabs || k.hide(), s.autoplay && setInterval(d.next, s.autoplay), f.bind("mousewheel", function (a, b, c, e) {
                return b > 0 ? d.next() : b < 0 && d.prev(), !1
            })
        }, d.selectTab = function (a,dir) {
            r = !1;
	    if(dir==undefined)dir="forward";
            var b = typeof a == "number" ? h.children("li:nth-child(" + a + ")") : a,
	    	c1=b.find("a").attr("href").split("#");
		if(c1[1] != undefined) var c = c1[1];
		else {if(dir=="reverse")d.selectTab(a-1);else d.selectTab(a+1);return;}
                var e = l.children("#" + c);//alert(c);
            d.selectedTab = typeof a == "number" ? a : a.index() + 1, A(e), q = !0;
            var f = h.find(".tabactive").index() < b.index() ? "left" : "right";
            b.siblings().removeClass("tabactive"), s.classes.tabActive != "" && b.siblings().removeClass(s.classes.tabActive), b.addClass("tabactive").addClass(s.classes.tabActive), y(l.children(".ui-slider-tab-content:visible"), f), z(e), v(b), u()
        }, d.next = function () {
            q || (d.count === d.selectedTab ? d.selectTab(1) : d.selectTab(d.selectedTab + 1))
        }, d.prev = function () {
            q || (d.selectedTab === 1 ? d.selectTab(d.count,"reverse") : d.selectTab((d.selectedTab - 1),"reverse"))
        }, d.slideTabs = function (a, b) {
            var c = parseInt(h.css("margin-left")),
                d = c;
            m.removeClass("edge"), n.removeClass("edge"), a == "right" ? d += b : a == "left" && (d -= b), d >= 0 ? (d = 0, m.addClass("edge")) : d <= t && (d = t, n.addClass("edge")), h.animate({
                "margin-left": d
            }, s.tabSlideSpeed)
        }, d.showIndicators = function () {
            if (!g) {
                g = a("<div class='ui-slider-tabs-indicator-container'>");
                for (var b = 0; b < i.length; b++) g.append("<div class='ui-slider-tabs-indicator'></div>");
                l.append(g)
            } else g.show()
        }, d.hideIndicators = function () {
            g && g.hide()
        }, d.showTabArrows = function () {
            if (!s.tabArrows) return;
            m.show(), n.show(), j.css("margin", "0 " + s.tabArrowWidth + "px")
        }, d.hideTabArrows = function () {
            m.hide(), n.hide(), j.css("margin", "0")
        }, d.showPanelArrows = function () {
            o && o.show(), p && p.show()
        }, d.hidePanelArrows = function () {
            o && o.hide(), p && p.hide()
        };
        var u = function () {
                if (s.indicators && g) {
                    var a = g.children("div:nth-child(" + d.selectedTab + ")");
                    a.siblings().removeClass("tabactive"), a.addClass("tabactive")
                }
            },
            v = function (a) {
                var b = a.offset(),
                    c = j.offset(),
                    e = b.left - c.left,
                    f = c.left + j.outerWidth() - (b.left + a.outerWidth());
                e < 0 ? d.slideTabs("right", -e) : f < 0 && d.slideTabs("left", -f)
            },
            w = function () {
                s.transition == "slide" && h.children("li").each(function (b, c) {
                    var d = h.children(".tabactive").index(),
                        e = a(c).index(),
			f1=a(c).find("a").attr("href").split("#"),
                        f = l.children("#" + f1[1]);
                   /* d < e ? f.css({
                        left: l.width() + "px"
                    }) : d > e ? f.css({
                        left: "-" + l.width() + "px"
                    }) : f.addClass(s.classes.panelActive)*/
			// Added For Linkable Tabs 
			if(d < e) f.css({left: l.width() + "px"});
			else {
				if(d > e){
					f.css({left: "-" + l.width() + "px"});				
				}
				else{
					f.css({left: "0px"});						
					f.addClass(s.classes.panelActive);				
				}
			}
                }), s.transition == "fade" && h.children("li").each(function (b, c) {
                    var d = h.children(".tabactive").index(),
                        e = a(c).index(),
			f1=a(c).find("a").attr("href").split("#"),
                        f = l.children("#" + f1[1]);
                    d != e ? f.css({
                        opacity: 0
                    }) : f.addClass(s.classes.panelActive)
                })
            },
            x = function (a) {
                return {
                    hide: {
                        slideleft: {
                            left: "-" + a + "px"
                        },
                        slideright: {
                            left: a + "px"
                        },
                        fade: {
                            opacity: 0
                        }
                    },
                    show: {
                        slide: {
                            left: 0
                        },
                        fade: {
                            opacity: 1
                        }
                    }
                }
            },
            y = function (a, b) {
                if (s.transition == "slide") var c = "slide" + b;
                else var c = s.transition;
                a.animate(x(l.width()).hide[c], s.transitionSpeed, s.transitionEasing, function () {
                    a.hide(), a.removeClass("tabactive"), q = !1, w()
                })
            },
            z = function (a) {
                a.show(), a.addClass(s.classes.panelActive).addClass("tabactive"), a.animate(x(l.width()).show[s.transition], s.transitionSpeed, s.transitionEasing, function () {
                    q = !1, r = !0, w()
                })
            },
            A = function (a) {
                s.height || l.animate({
                    height: E(a)
                }, 200)
            },
            B = function () {
                s.panelArrows && (!o && !p && (o = a("<div class='ui-slider-tabs-leftPanelArrow'>").click(function () {
                    d.prev()
                }), p = a("<div class='ui-slider-tabs-rightPanelArrow'>").click(function () {
                    d.next()
                }), o.appendTo(l), p.appendTo(l)), p.css({
                    top: l.height() / 2 - p.outerHeight() / 2
                }), o.css({
                    top: l.height() / 2 - o.outerHeight() / 2
                }))
            },
            C = function () {
                var b = 0;
                h.children().each(function (c, d) {
                    b += a(d).outerWidth(!0)
                }), h.width(b), j.width() < b && s.tabArrows ? (d.showTabArrows(), t = j.width() - b) : d.hideTabArrows()
            },
            D = function () {
                i.width(l.width() - (i.outerWidth() - i.width()))
            },
            E = function (a) {
                var b = {
                    display: a.css("display"),
                    left: a.css("left"),
                    position: a.css("position")
                };
                a.css({
                    display: "normal",
                    left: -5e3,
                    position: "absolute"
                });
                var c = a.outerHeight();
                return a.css(b), c
            };
        d.init()
    }, a.fn.sliderTabs = function (b) {
        return this.each(function () {
            var c = a(this),
                d = c.data("sliderTabs");
            if (!d) return d = new a.sliderTabs(this, b), c.data("sliderTabs", d), d;
            if (d.methods[b]) return d.methods[b].apply(this, Array.prototype.slice.call(arguments, 1))
        })
    }
})(jQuery), jQuery.extend(jQuery.easing, {
    def: "easeOutQuad",
    swing: function (a, b, c, d, e) {
        return jQuery.easing[jQuery.easing.def](a, b, c, d, e)
    },
    easeInQuad: function (a, b, c, d, e) {
        return d * (b /= e) * b + c
    },
    easeOutQuad: function (a, b, c, d, e) {
        return -d * (b /= e) * (b - 2) + c
    },
    easeInOutQuad: function (a, b, c, d, e) {
        return (b /= e / 2) < 1 ? d / 2 * b * b + c : -d / 2 * (--b * (b - 2) - 1) + c
    },
    easeInCubic: function (a, b, c, d, e) {
        return d * (b /= e) * b * b + c
    },
    easeOutCubic: function (a, b, c, d, e) {
        return d * ((b = b / e - 1) * b * b + 1) + c
    },
    easeInOutCubic: function (a, b, c, d, e) {
        return (b /= e / 2) < 1 ? d / 2 * b * b * b + c : d / 2 * ((b -= 2) * b * b + 2) + c
    },
    easeInQuart: function (a, b, c, d, e) {
        return d * (b /= e) * b * b * b + c
    },
    easeOutQuart: function (a, b, c, d, e) {
        return -d * ((b = b / e - 1) * b * b * b - 1) + c
    },
    easeInOutQuart: function (a, b, c, d, e) {
        return (b /= e / 2) < 1 ? d / 2 * b * b * b * b + c : -d / 2 * ((b -= 2) * b * b * b - 2) + c
    },
    easeInQuint: function (a, b, c, d, e) {
        return d * (b /= e) * b * b * b * b + c
    },
    easeOutQuint: function (a, b, c, d, e) {
        return d * ((b = b / e - 1) * b * b * b * b + 1) + c
    },
    easeInOutQuint: function (a, b, c, d, e) {
        return (b /= e / 2) < 1 ? d / 2 * b * b * b * b * b + c : d / 2 * ((b -= 2) * b * b * b * b + 2) + c
    },
    easeInSine: function (a, b, c, d, e) {
        return -d * Math.cos(b / e * (Math.PI / 2)) + d + c
    },
    easeOutSine: function (a, b, c, d, e) {
        return d * Math.sin(b / e * (Math.PI / 2)) + c
    },
    easeInOutSine: function (a, b, c, d, e) {
        return -d / 2 * (Math.cos(Math.PI * b / e) - 1) + c
    },
    easeInExpo: function (a, b, c, d, e) {
        return b == 0 ? c : d * Math.pow(2, 10 * (b / e - 1)) + c
    },
    easeOutExpo: function (a, b, c, d, e) {
        return b == e ? c + d : d * (-Math.pow(2, -10 * b / e) + 1) + c
    },
    easeInOutExpo: function (a, b, c, d, e) {
        return b == 0 ? c : b == e ? c + d : (b /= e / 2) < 1 ? d / 2 * Math.pow(2, 10 * (b - 1)) + c : d / 2 * (-Math.pow(2, -10 * --b) + 2) + c
    },
    easeInCirc: function (a, b, c, d, e) {
        return -d * (Math.sqrt(1 - (b /= e) * b) - 1) + c
    },
    easeOutCirc: function (a, b, c, d, e) {
        return d * Math.sqrt(1 - (b = b / e - 1) * b) + c
    },
    easeInOutCirc: function (a, b, c, d, e) {
        return (b /= e / 2) < 1 ? -d / 2 * (Math.sqrt(1 - b * b) - 1) + c : d / 2 * (Math.sqrt(1 - (b -= 2) * b) + 1) + c
    },
    easeInElastic: function (a, b, c, d, e) {
        var f = 1.70158,
            g = 0,
            h = d;
        if (b == 0) return c;
        if ((b /= e) == 1) return c + d;
        g || (g = e * .3);
        if (h < Math.abs(d)) {
            h = d;
            var f = g / 4
        } else var f = g / (2 * Math.PI) * Math.asin(d / h);
        return -(h * Math.pow(2, 10 * (b -= 1)) * Math.sin((b * e - f) * 2 * Math.PI / g)) + c
    },
    easeOutElastic: function (a, b, c, d, e) {
        var f = 1.70158,
            g = 0,
            h = d;
        if (b == 0) return c;
        if ((b /= e) == 1) return c + d;
        g || (g = e * .3);
        if (h < Math.abs(d)) {
            h = d;
            var f = g / 4
        } else var f = g / (2 * Math.PI) * Math.asin(d / h);
        return h * Math.pow(2, -10 * b) * Math.sin((b * e - f) * 2 * Math.PI / g) + d + c
    },
    easeInOutElastic: function (a, b, c, d, e) {
        var f = 1.70158,
            g = 0,
            h = d;
        if (b == 0) return c;
        if ((b /= e / 2) == 2) return c + d;
        g || (g = e * .3 * 1.5);
        if (h < Math.abs(d)) {
            h = d;
            var f = g / 4
        } else var f = g / (2 * Math.PI) * Math.asin(d / h);
        return b < 1 ? -0.5 * h * Math.pow(2, 10 * (b -= 1)) * Math.sin((b * e - f) * 2 * Math.PI / g) + c : h * Math.pow(2, -10 * (b -= 1)) * Math.sin((b * e - f) * 2 * Math.PI / g) * .5 + d + c
    },
    easeInBack: function (a, b, c, d, e, f) {
        return f == undefined && (f = 1.70158), d * (b /= e) * b * ((f + 1) * b - f) + c
    },
    easeOutBack: function (a, b, c, d, e, f) {
        return f == undefined && (f = 1.70158), d * ((b = b / e - 1) * b * ((f + 1) * b + f) + 1) + c
    },
    easeInOutBack: function (a, b, c, d, e, f) {
        return f == undefined && (f = 1.70158), (b /= e / 2) < 1 ? d / 2 * b * b * (((f *= 1.525) + 1) * b - f) + c : d / 2 * ((b -= 2) * b * (((f *= 1.525) + 1) * b + f) + 2) + c
    },
    easeInBounce: function (a, b, c, d, e) {
        return d - jQuery.easing.easeOutBounce(a, e - b, 0, d, e) + c
    },
    easeOutBounce: function (a, b, c, d, e) {
        return (b /= e) < 1 / 2.75 ? d * 7.5625 * b * b + c : b < 2 / 2.75 ? d * (7.5625 * (b -= 1.5 / 2.75) * b + .75) + c : b < 2.5 / 2.75 ? d * (7.5625 * (b -= 2.25 / 2.75) * b + .9375) + c : d * (7.5625 * (b -= 2.625 / 2.75) * b + .984375) + c
    },
    easeInOutBounce: function (a, b, c, d, e) {
        return b < e / 2 ? jQuery.easing.easeInBounce(a, b * 2, 0, d, e) * .5 + c : jQuery.easing.easeOutBounce(a, b * 2 - e, 0, d, e) * .5 + d * .5 + c
    }
}),
function (a) {
    function d(b) {
        var c = b || window.event,
            d = [].slice.call(arguments, 1),
            e = 0,
            f = !0,
            g = 0,
            h = 0;
        return b = a.event.fix(c), b.type = "mousewheel", c.wheelDelta && (e = c.wheelDelta / 120), c.detail && (e = -c.detail / 3), h = e, c.axis !== undefined && c.axis === c.HORIZONTAL_AXIS && (h = 0, g = -1 * e), c.wheelDeltaY !== undefined && (h = c.wheelDeltaY / 120), c.wheelDeltaX !== undefined && (g = -1 * c.wheelDeltaX / 120), d.unshift(b, e, g, h), (a.event.dispatch || a.event.handle).apply(this, d)
    }
    var b = ["DOMMouseScroll", "mousewheel"];
    if (a.event.fixHooks)
        for (var c = b.length; c;) a.event.fixHooks[b[--c]] = a.event.mouseHooks;
    a.event.special.mousewheel = {
        setup: function () {
            if (this.addEventListener)
                for (var a = b.length; a;) this.addEventListener(b[--a], d, !1);
            else this.onmousewheel = d
        },
        teardown: function () {
            if (this.removeEventListener)
                for (var a = b.length; a;) this.removeEventListener(b[--a], d, !1);
            else this.onmousewheel = null
        }
    }, a.fn.extend({
        mousewheel: function (a) {
            return a ? this.bind("mousewheel", a) : this.trigger("mousewheel")
        },
        unmousewheel: function (a) {
            return this.unbind("mousewheel", a)
        }
    })
}(jQuery),
function (a, b, c) {
    function l() {
        f = b[g](function () {
            d.each(function () {
                var b = a(this),
                    c = b.width(),
                    d = b.height(),
                    e = a.data(this, i);
                (c !== e.w || d !== e.h) && b.trigger(h, [e.w = c, e.h = d])
            }), l()
        }, e[j])
    }
    var d = a([]),
        e = a.resize = a.extend(a.resize, {}),
        f, g = "setTimeout",
        h = "resize",
        i = h + "-special-event",
        j = "delay",
        k = "throttleWindow";
    e[j] = 250, e[k] = !0, a.event.special[h] = {
        setup: function () {
            if (!e[k] && this[g]) return !1;
            var b = a(this);
            d = d.add(b), a.data(this, i, {
                w: b.width(),
                h: b.height()
            }), d.length === 1 && l()
        },
        teardown: function () {
            if (!e[k] && this[g]) return !1;
            var b = a(this);
            d = d.not(b), b.removeData(i), d.length || clearTimeout(f)
        },
        add: function (b) {
            function f(b, e, f) {
                var g = a(this),
                    h = a.data(this, i);
                h.w = e !== c ? e : g.width(), h.h = f !== c ? f : g.height(), d.apply(this, arguments)
            }
            if (!e[k] && this[g]) return !1;
            var d;
            if (a.isFunction(b)) return d = b, f;
            d = b.handler, b.handler = f
        }
    }
}(jQuery, this)
