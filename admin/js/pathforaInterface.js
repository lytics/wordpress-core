var h = function() {
  function f(o, i, l) {
    o === void 0 && (o = false), i === void 0 && (i = false), l === void 0 && (l = "");
    var c = this;
    i && (console.log("Adding JSTAG shim to the page"), this.loadJSTAGLibrary(l).then(function() {
      console.log("JSTAG loaded successfully");
    }).catch(function(t) {
      console.error("Error loading JSTAG script:", t);
    })), o && (console.log("Adding Pathfora shim to the page"), this.loadPathforaLibrary("https://c.lytics.io/static/pathfora.js").then(function() {
      c.client = window.pathfora, console.log("Pathfora loaded successfully");
    }).catch(function(t) {
      console.error("Error loading Pathfora script:", t);
    }));
  }
  return f.prototype.loadPathforaLibrary = function(o) {
    return new Promise(function(i, l) {
      var c = document.createElement("script");
      c.src = o, c.onload = function() {
        return i();
      }, c.onerror = function() {
        return l("Error loading script: ".concat(o));
      }, document.body.appendChild(c);
    });
  }, f.prototype.loadJSTAGLibrary = function(o) {
    return new Promise(function(i, l) {
      var c = document.createElement("script");
      c.textContent = `!function(){"use strict";var o=window.jstag||(window.jstag={}),r=[];function n(e){o[e]=function(){for(var n=arguments.length,t=new Array(n),i=0;i<n;i++)t[i]=arguments[i];r.push([e,t])}}n("send"),n("mock"),n("identify"),n("pageView"),n("unblock"),n("getid"),n("setid"),n("loadEntity"),n("getEntity"),n("on"),n("once"),n("call"),o.loadScript=function(n,t,i){var e=document.createElement("script");e.async=!0,e.src=n,e.onload=t,e.onerror=i;var o=document.getElementsByTagName("script")[0],r=o&&o.parentNode||document.head||document.body,c=o||r.lastChild;return null!=c?r.insertBefore(e,c):r.appendChild(e),this},o.init=function n(t){return this.config=t,this.loadScript(t.src,function(){if(o.init===n)throw new Error("Load error!");o.init(o.config),function(){for(var n=0;n<r.length;n++){var t=r[n][0],i=r[n][1];o[t].apply(o,i)}r=void 0}()}),this}}();
      jstag.init({
        src: 'https://c.lytics.io/api/tag/`.concat(o, `/latest.min.js',
        stream: 'drupal-widget-test'
      });`), document.body.appendChild(c), i();
    });
  }, f.prototype.serializeWidget = function(o) {
    o.config.confirmAction && o.config.confirmAction.callback && (o.config.confirmAction.callback = o.config.confirmAction.callback.toString()), o.config.cancelAction && o.config.cancelAction.callback && (o.config.cancelAction.callback = o.config.cancelAction.callback.toString()), o.config.closeAction && o.config.closeAction.callback && (o.config.closeAction.callback = o.config.closeAction.callback.toString()), o.config.onInit && (o.config.onInit = o.config.onInit.toString()), o.config.onLoad && (o.config.onLoad = o.config.onLoad.toString()), o.config.onClick && (o.config.onClick = o.config.onClick.toString()), o.config.onModalClose && (o.config.onModalClose = o.config.onModalClose.toString());
    var i = JSON.stringify(o);
    return i;
  }, f.prototype.deserializeWidget = function(o) {
    var i, l, c, t, a, s, n, r, d, u;
    typeof o == "string" && (o = JSON.parse(o));
    var e = function(v) {
      try {
        return new Function("return ".concat(v))();
      } catch (p) {
        return console.error("Error creating function:", p), null;
      }
    };
    return !((l = (i = o == null ? void 0 : o.config) === null || i === void 0 ? void 0 : i.confirmAction) === null || l === void 0) && l.callback && (o.config.confirmAction.callback = e(o.config.confirmAction.callback)), !((t = (c = o == null ? void 0 : o.config) === null || c === void 0 ? void 0 : c.cancelAction) === null || t === void 0) && t.callback && (o.config.cancelAction.callback = e(o.config.cancelAction.callback)), !((s = (a = o == null ? void 0 : o.config) === null || a === void 0 ? void 0 : a.closeAction) === null || s === void 0) && s.callback && (o.config.closeAction.callback = e(o.config.closeAction.callback)), !((n = o == null ? void 0 : o.config) === null || n === void 0) && n.onInit && (o.config.onInit = e(o.config.onInit)), !((r = o == null ? void 0 : o.config) === null || r === void 0) && r.onLoad && (o.config.onLoad = e(o.config.onLoad)), !((d = o == null ? void 0 : o.config) === null || d === void 0) && d.onClick && (o.config.onClick = e(o.config.onClick)), !((u = o == null ? void 0 : o.config) === null || u === void 0) && u.onModalClose && (o.config.onModalClose = e(o.config.onModalClose)), o;
  }, f.prototype.testWidget = function(o) {
    var i, l, c, t, a, s, n = o.config;
    o.details, n.id = "test-widget", !((i = n == null ? void 0 : n.displayConditions) === null || i === void 0) && i.urlContains && (n.displayConditions.urlContains = []), !((l = n == null ? void 0 : n.displayConditions) === null || l === void 0) && l.hideAfterAction && (n.displayConditions.hideAfterAction = {}), !((c = n == null ? void 0 : n.displayConditions) === null || c === void 0) && c.showOnExitIntent && (n.displayConditions.showOnExitIntent = false), !((t = n == null ? void 0 : n.displayConditions) === null || t === void 0) && t.impressions && (n.displayConditions.impressions = {}), !((a = n == null ? void 0 : n.displayConditions) === null || a === void 0) && a.pageVisits && (n.displayConditions.pageVisits = 0), !((s = n == null ? void 0 : n.displayConditions) === null || s === void 0) && s.scrollPercentageToDisplay && (n.displayConditions.scrollPercentageToDisplay = 0);
    var r;
    switch (o.details.type) {
      case "message":
        r = new this.client.Message(n);
        break;
      case "recommendation":
        r = new this.client.Message(n);
        break;
      case "form":
        r = new this.client.Form(n);
        break;
      default:
        console.error("Unsupported widget type:", o.details.type);
    }
    this.client.initializeWidgets([r]);
  }, f;
}();
export {
  h
};
