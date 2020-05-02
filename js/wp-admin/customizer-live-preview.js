/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 * Things like site title and description changes.
 */

(function($) {

  var parser = new CSSParser(),
      sheet = parser.parse(customizerVars.cssTemplate, false, true),
      parseLESS,
      applyCSS,
      cssPropertyCallback;

  parseLESS = function (variable, value) {
    var output = [],
        outputCSS = '';

    $.each(sheet.cssRules, function () {
      var rule = this;

      $.each(rule.declarations, function () {
        declaration = this;

        if (declaration.valueText == '@' + variable) {
          output.push({
            selector: rule.mSelectorText,
            property: declaration.property,
            value: value
          });
        }
      });
    });

    $.each(output, function () {
      outputCSS += this.selector + " {\n" + this.property + ": " + this.value + ";\n}\n\n";
    });

    return outputCSS;
  };

  applyCSS = function (css) {
    var $head = $('head'),
        $style = $('<style />');

    if ($style[0].styleSheet) {
      $style[0].styleSheet.cssText = css;
    } else {
      $style[0].appendChild(document.createTextNode(css));
    }

    $head.append($style);
  };

  createCSSPropertyListener = function (variable, value) {
    wp.customize(variable, function (callback) {
      callback.bind(function (value) {
        applyCSS(parseLESS(variable, value));
      });
    });
  };

  /**
   * CSS Properties
   */
  createCSSPropertyListener('css_accent_color');
  createCSSPropertyListener('css_accent_alt_color');

  /**
   * Skin
   */
  wp.customize('skin', function (callback) {
    callback.bind(function (value) {

      var $skin = $('link#fluxus-skin-css'),
          $user = $('link#fluxus-user-css');

      if ($skin.length) {
        $skin.remove();
      }

      $skin = $('<link />').attr({
        rel: 'stylesheet',
        id: 'fluxus-skin-css',
        type: 'text/css',
        media: 'all',
        href: customizerVars.templateUrl + '/' + value
      });

      if ($user.length) {
        $skin.insertAfter($user);
      } else {
        $('head').append($skin);
      }

    });
  });

  /**
   * Default properties.
   */
  wp.customize('blogdescription', function (callback) {
    callback.bind(function (value) {
      $('.site-description').text(value);
    });
  });

  wp.customize('logo', function (callback) {
    callback.bind(function (value) {
      var img = new Image();
      $(img).load(function () {
        $('#header .logo').attr({
          src: value,
          width: img.width,
          height: img.height
        });
        $(window).resize();
      });

      img.src = value;
    });
  });

  wp.customize('logo_retina', function (callback) {
    callback.bind(function (value) {
      var img = new Image();
      $(img).load(function () {
        $('#header .logo').attr({
          src: value,
          width: img.width / 2,
          height: img.height / 2
        });
        $(window).resize();
      });

      img.src = value;
    });
  });

})(jQuery);