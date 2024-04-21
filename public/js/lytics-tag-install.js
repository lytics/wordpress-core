!(function () {
  "use strict";
  var o = window.jstag || (window.jstag = {}),
    r = [];
  function n(e) {
    o[e] = function () {
      for (var n = arguments.length, t = new Array(n), i = 0; i < n; i++)
        t[i] = arguments[i];
      r.push([e, t]);
    };
  }
  n("send"),
    n("mock"),
    n("identify"),
    n("pageView"),
    n("unblock"),
    n("getid"),
    n("setid"),
    n("loadEntity"),
    n("getEntity"),
    n("on"),
    n("once"),
    n("call"),
    (o.loadScript = function (n, t, i) {
      var e = document.createElement("script");
      (e.async = !0), (e.src = n), (e.onload = t), (e.onerror = i);
      var o = document.getElementsByTagName("script")[0],
        r = (o && o.parentNode) || document.head || document.body,
        c = o || r.lastChild;
      return null != c ? r.insertBefore(e, c) : r.appendChild(e), this;
    }),
    (o.init = function n(t) {
      return (
        (this.config = t),
        this.loadScript(t.src, function () {
          if (o.init === n) throw new Error("Load error!");
          o.init(o.config),
            (function () {
              for (var n = 0; n < r.length; n++) {
                var t = r[n][0],
                  i = r[n][1];
                o[t].apply(o, i);
              }
              r = void 0;
            })();
        }),
        this
      );
    });
})();

if (typeof lytics_tag_vars === "undefined") {
  console.warn(
    "Lytics expected to find configuration but no details found. Unable to initialize Lytics tag."
  );
} else {
  var _lytics_tag_config = lytics_tag_vars.config;

  if (_lytics_tag_config) {
    try {
      var config = JSON.parse(_lytics_tag_config);

      console.log(config);

      jstag.init(config);
      jstag.pageView();
    } catch (e) {
      console.warn("Unable to initialize Lytics with invalid config.");
    }
  } else {
    console.warn("Unable to initialize Lytics without config.");
  }
}
