var iCarousel = new Class({
	Implements: [Events,  Options],
    options: {
        animation: {
            type: "fadeNscroll",
            direction: "left",
            amount: 1,
            transition: Fx.Transitions.Cubic.easeInOut,
            duration: 500,
            rotate: {
                type: "manual",
                interval: 5000,
                onMouseOver: "stop"
            }
        },
        item: {
            klass: "item",
            size: 100
        },
        idPrevious: "previous",
        idNext: "next",
        idToggle: "toggle",
        onClickPrevious: Class.empty,
        onClickNext: Class.empty,
        onPrevious: Class.empty,
        onNext: Class.empty,
        onGoTo: Class.empty
    },
    initialize: function (_1, _2) {
        this.setOptions(_2);
        this.container = $(_1);
        this.aItems = $A($$("." + this.options.item.klass));
        this.isMouseOver = false;
        if (this.options.idPrevious != "undefined" && $(this.options.idPrevious)) {
            $(this.options.idPrevious).addEvent("click", function (_3) {
                new Event(_3).stop();
                this._previous();
                this.fireEvent("onClickPrevious", this, 20)
            }.bind(this))
        }
        if (this.options.idNext != "undefined" && $(this.options.idNext)) {
            $(this.options.idNext).addEvent("click", function (_4) {
                new Event(_4).stop();
                this._next();
                this.fireEvent("onClickNext", this, 20)
            }.bind(this))
        }
        if (this.options.idToggle != "undefined" && $(this.options.idToggle)) {
            $(this.options.idToggle).addEvent("click", function (_5) {
                new Event(_5).stop();
                this._toggle()
            }.bind(this))
        }
        var _6 = this.options.animation;
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            this.aItems.each(function (_7) {
                _7.fx = _7.effect("opacity", {
                    duration: _6.duration,
                    transition: _6.transition
                });
                _7.setStyle("opacity", 0);
                _7.addEvents({
                    "mouseenter": function () {
                        this.isMouseOver = true;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = $clear(this.timer)
                        }
                    }.bind(this),
                    "mouseleave": function () {
                        this.isMouseOver = false;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this)
                        }
                    }.bind(this)
                })
            }.bind(this));
            this.height = this.container.getStyle("height").toInt();
            this.atScreen = 0;
            this._animate(this.atScreen);
            break;
        default:
            (2).times(function () {
                this.aItems.each(function (_8) {
                    _8.clone().injectInside(this.container)
                }.bind(this))
            }.bind(this));
            this.aItems = $A($$("." + this.options.item.klass));
            this.aItems.each(function (_9) {
                _9.addEvents({
                    "mouseenter": function () {
                        this.isMouseOver = true;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = $clear(this.timer)
                        }
                    }.bind(this),
                    "mouseleave": function () {
                        this.isMouseOver = false;
                        if (this.options.animation.rotate.type == "auto") {
                            this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this)
                        }
                    }.bind(this)
                })
            }.bind(this));
            this.fx = new Fx.Morph(this.container, {
                duration: _6.duration,
                transition: _6.transition,
                link: 'cancel'
            });
            this.atScreen = this.aItems.length / 3;
            this.container.setStyle(_6.direction, -this.atScreen * this.options.item.size);
            break
        }
        if (this.options.animation.rotate.type == "auto") {
            this.timer = this._autoRotate.periodical(this.options.animation.rotate.interval, this)
        }
    },
    goTo: function (n) {
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            var _b = this.atScreen;
            this.atScreen = Math.abs(n % (this.aItems.length / 3));
            this._animate(this.atScreen, _b);
            break;
        default:
            this.atScreen = Math.abs(n % (this.aItems.length / 3));
            this.atScreen += this.aItems.length / 3;
            this._animate(this.atScreen);
            break
        }
        this.fireEvent("onGoTo", this, 20)
    },
    _previous: function () {
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            var _c = this.atScreen;
            this.atScreen -= this.options.animation.amount;
            if (this.atScreen < 0) {
                this.atScreen = (this.aItems.length - 1)
            }
            this._animate(this.atScreen, _c);
            break;
        default:
            this.atScreen -= this.options.animation.amount;
            if (this.atScreen < this.aItems.length / 3) {
                this.container.setStyle(this.options.animation.direction, -this.options.item.size * this.aItems.length * 2 / 3);
                this.atScreen = this.aItems.length * 2 / 3 - this.options.animation.amount
            }
            this._animate(this.atScreen);
            break
        }
        this.fireEvent("onPrevious", this, 20)
    },
    _next: function () {
        switch (this.options.animation.type.toLowerCase()) {
        case "fade":
            var _d = this.atScreen;
            this.atScreen += this.options.animation.amount;
            if (this.atScreen >= this.aItems.length) {
                this.atScreen = 0
            }
            this._animate(this.atScreen, _d);
            break;
        default:
            this.atScreen += this.options.animation.amount;
            if (this.atScreen > this.aItems.length * 2 / 3) {
                this.container.setStyle(this.options.animation.direction, -this.options.item.size * this.aItems.length / 3);
                this.atScreen = this.aItems.length / 3 + this.options.animation.amount
            }
            this._animate(this.atScreen);
            break
        }
        this.fireEvent("onNext", this, 20)
    },
    _toggle: function () {
        (this.container.getStyle("height").toInt() == 0) ? new Fx.Tween(this.container, {
			property: 'height',
            duration: 1000,
            transition: Fx.Transitions.Sine.easeInOut
        }).start(this.height) : new Fx.Tween(this.container, {
			property: 'height',
            duration: 1000,
            transition: Fx.Transitions.Sine.easeInOut
        }).start(0)
    },
    _autoRotate: function () {
        if (this.options.animation.rotate.onMouseOver == "stop" && !this.isMouseOver) {
            this._next()
        }
    },
    _animate: function (a, b) {
        switch (this.options.animation.type) {
        case "fade":
            if ($defined(b)) {
                this.aItems[b].fx.start(0).chain(function () {
                    this.aItems[a].fx.start(1)
                }.bind(this))
            } else {
                this.aItems[a].fx.start(1)
            }
            break;
        case "scroll":
            var _10 = this;
            if (_10.options.animation.direction == "top") {
                _10.fx.start({
                    "top": -a * _10.options.item.size
                })
            } else {
                _10.fx.start({
                    "left": -a * _10.options.item.size
                })
            }
            break;
        case "fadeNscroll":
            var _10 = this;
            if (_10.options.animation.direction == "top") {
                _10.fx.start({
                    "opacity": 0.75
                }).chain(function () {
                    _10.fx.start({
                        "top": -a * _10.options.item.size
                    }).chain(function () {
                        _10.fx.start({
                            "opacity": 1
                        })
                    })
                })
            } else {
                _10.fx.start({
                    "opacity": 0.75
                }).chain(function () {
                    _10.fx.start({
                        "left": -a * _10.options.item.size
                    }).chain(function () {
                        _10.fx.start({
                            "opacity": 1
                        })
                    })
                })
            }
            break
        }
    }
});