/*
    A simple jQuery modal (http://github.com/kylefox/jquery-modal)
    Version 0.9.2
*/
(function (factory) {
  // Making your jQuery plugin work better with npm tools
  // http://blog.npmjs.org/post/112712169830/making-your-jquery-plugin-work-better-with-npm
  if(typeof module === "object" && typeof module.exports === "object") {
    factory(require("jquery"), window, document);
  }
  else {
    factory(jQuery, window, document);
  }
}(function($, window, document, undefined) {

  var whalestackmodals = [],
      getCurrent = function() {
        return whalestackmodals.length ? whalestackmodals[whalestackmodals.length - 1] : null;
      },
      selectCurrent = function() {
        var i,
            selected = false;
        for (i=whalestackmodals.length-1; i>=0; i--) {
          if (whalestackmodals[i].$blocker) {
            whalestackmodals[i].$blocker.toggleClass('current',!selected).toggleClass('behind',selected);
            selected = true;
          }
        }
      };

  $.whalestackmodal = function(el, options) {
    var remove, target;
    this.$body = $('body');
    this.options = $.extend({}, $.whalestackmodal.defaults, options);
    this.options.doFade = !isNaN(parseInt(this.options.fadeDuration, 10));
    this.$blocker = null;
    if (this.options.closeExisting)
      while ($.whalestackmodal.isActive())
        $.whalestackmodal.close(); // Close any open whalestackmodals.
    whalestackmodals.push(this);
    if (el.is('a')) {
      target = el.attr('href');
      this.anchor = el;
      //Select element by id from href
      if (/^#/.test(target)) {
        this.$elm = $(target);
        if (this.$elm.length !== 1) return null;
        this.$body.append(this.$elm);
        this.open();
      //AJAX
      } else {
        this.$elm = $('<div>');
        this.$body.append(this.$elm);
        remove = function(event, modal) { modal.elm.remove(); };
        this.showSpinner();
        el.trigger($.whalestackmodal.AJAX_SEND);
        $.get(target).done(function(html) {
          if (!$.whalestackmodal.isActive()) return;
          el.trigger($.whalestackmodal.AJAX_SUCCESS);
          var current = getCurrent();
          current.$elm.empty().append(html).on($.whalestackmodal.CLOSE, remove);
          current.hideSpinner();
          current.open();
          el.trigger($.whalestackmodal.AJAX_COMPLETE);
        }).fail(function() {
          el.trigger($.whalestackmodal.AJAX_FAIL);
          var current = getCurrent();
          current.hideSpinner();
          whalestackmodals.pop(); // remove expected modal from the list
          el.trigger($.whalestackmodal.AJAX_COMPLETE);
        });
      }
    } else {
      this.$elm = el;
      this.anchor = el;
      this.$body.append(this.$elm);
      this.open();
    }
  };

  $.whalestackmodal.prototype = {
    constructor: $.whalestackmodal,

    open: function() {
      var m = this;
      this.block();
      this.anchor.blur();
      if(this.options.doFade) {
        setTimeout(function() {
          m.show();
        }, this.options.fadeDuration * this.options.fadeDelay);
      } else {
        this.show();
      }
      $(document).off('keydown.whalestackmodal').on('keydown.whalestackmodal', function(event) {
        var current = getCurrent();
        if (event.which === 27 && current.options.escapeClose) current.close();
      });
      if (this.options.clickClose)
        this.$blocker.click(function(e) {
          if (e.target === this)
            $.whalestackmodal.close();
        });
    },

    close: function() {
      whalestackmodals.pop();
      this.unblock();
      this.hide();
      if (!$.whalestackmodal.isActive())
        $(document).off('keydown.whalestackmodal');
    },

    block: function() {
      this.$elm.trigger($.whalestackmodal.BEFORE_BLOCK, [this._ctx()]);
      this.$body.css('overflow','hidden');
      this.$blocker = $('<div class="' + this.options.blockerClass + ' blocker current"></div>').appendTo(this.$body);
      selectCurrent();
      if(this.options.doFade) {
        this.$blocker.css('opacity',0).animate({opacity: 1}, this.options.fadeDuration);
      }
      this.$elm.trigger($.whalestackmodal.BLOCK, [this._ctx()]);
    },

    unblock: function(now) {
      if (!now && this.options.doFade)
        this.$blocker.fadeOut(this.options.fadeDuration, this.unblock.bind(this,true));
      else {
        this.$blocker.children().appendTo(this.$body);
        this.$blocker.remove();
        this.$blocker = null;
        selectCurrent();
        if (!$.whalestackmodal.isActive())
          this.$body.css('overflow','');
      }
    },

    show: function() {
      this.$elm.trigger($.whalestackmodal.BEFORE_OPEN, [this._ctx()]);
      if (this.options.showClose) {
        this.closeButton = $('<a href="#whalestack-close-modal" rel="whalestackmodal:close" class="whalestack-close-modal ' + this.options.closeClass + '">' + this.options.closeText + '</a>');
        this.$elm.append(this.closeButton);
      }
      this.$elm.addClass(this.options.modalClass).appendTo(this.$blocker);
      if(this.options.doFade) {
        this.$elm.css({opacity: 0, display: 'inline-block'}).animate({opacity: 1}, this.options.fadeDuration);
      } else {
        this.$elm.css('display', 'inline-block');
      }
      this.$elm.trigger($.whalestackmodal.OPEN, [this._ctx()]);
    },

    hide: function() {
      this.$elm.trigger($.whalestackmodal.BEFORE_CLOSE, [this._ctx()]);
      if (this.closeButton) this.closeButton.remove();
      var _this = this;
      if(this.options.doFade) {
        this.$elm.fadeOut(this.options.fadeDuration, function () {
          _this.$elm.trigger($.whalestackmodal.AFTER_CLOSE, [_this._ctx()]);
        });
      } else {
        this.$elm.hide(0, function () {
          _this.$elm.trigger($.whalestackmodal.AFTER_CLOSE, [_this._ctx()]);
        });
      }
      this.$elm.trigger($.whalestackmodal.CLOSE, [this._ctx()]);
    },

    showSpinner: function() {
      if (!this.options.showSpinner) return;
      this.spinner = this.spinner || $('<div class="' + this.options.modalClass + '-spinner"></div>')
        .append(this.options.spinnerHtml);
      this.$body.append(this.spinner);
      this.spinner.show();
    },

    hideSpinner: function() {
      if (this.spinner) this.spinner.remove();
    },

    //Return context for custom events
    _ctx: function() {
      return { elm: this.$elm, $elm: this.$elm, $blocker: this.$blocker, options: this.options, $anchor: this.anchor };
    }
  };

  $.whalestackmodal.close = function(event) {
    if (!$.whalestackmodal.isActive()) return;
    if (event) event.preventDefault();
    var current = getCurrent();
    current.close();
    return current.$elm;
  };

  // Returns if there currently is an active modal
  $.whalestackmodal.isActive = function () {
    return whalestackmodals.length > 0;
  };

  $.whalestackmodal.getCurrent = getCurrent;

  $.whalestackmodal.defaults = {
    closeExisting: true,
    escapeClose: false,
    clickClose: false,
    closeText: 'Close',
    closeClass: '',
    modalClass: "whalestack-modal",
    blockerClass: "whalestack-jquery-modal",
    spinnerHtml: '<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div>',
    showSpinner: false,
    showClose: true,
    fadeDuration: null,   // Number of milliseconds the fade animation takes.
    fadeDelay: 1.0        // Point during the overlay's fade-in that the modal begins to fade in (.5 = 50%, 1.5 = 150%, etc.)
  };

  // Event constants
  $.whalestackmodal.BEFORE_BLOCK = 'whalestackmodal:before-block';
  $.whalestackmodal.BLOCK = 'whalestackmodal:block';
  $.whalestackmodal.BEFORE_OPEN = 'whalestackmodal:before-open';
  $.whalestackmodal.OPEN = 'whalestackmodal:open';
  $.whalestackmodal.BEFORE_CLOSE = 'whalestackmodal:before-close';
  $.whalestackmodal.CLOSE = 'whalestackmodal:close';
  $.whalestackmodal.AFTER_CLOSE = 'whalestackmodal:after-close';
  $.whalestackmodal.AJAX_SEND = 'whalestackmodal:ajax:send';
  $.whalestackmodal.AJAX_SUCCESS = 'whalestackmodal:ajax:success';
  $.whalestackmodal.AJAX_FAIL = 'whalestackmodal:ajax:fail';
  $.whalestackmodal.AJAX_COMPLETE = 'whalestackmodal:ajax:complete';

  $.fn.whalestack_modal = function(options){
    if (this.length === 1) {
      new $.whalestackmodal(this, options);
    }
    return this;
  };

  // Automatically bind links with rel="modal:close" to, well, close the modal.
  $(document).on('click.whalestackmodal', 'a[rel~="whalestackmodal:close"]', $.whalestackmodal.close);
  $(document).on('click.whalestackmodal', 'a[rel~="whalestackmodal:open"]', function(event) {
    event.preventDefault();
    $(this).whalestack_modal();
  });
}));
