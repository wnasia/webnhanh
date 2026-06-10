<?php
/*
Plugin Name: Nuke GTM Clarity (Clean Loader)
Description: Chặn mọi Clarity có ?ref=gtm và nạp lại Clarity sạch (p132zuhmqp) theo kiểu delay. Dán file này vào wp-content/mu-plugins/gtm-guard.php
*/

/* 1) CHẶN runtime: mọi <script src="https://www.clarity.ms/tag/... ?ref=gtm"> */
add_action('wp_head', function(){ ?>
<script>
(function(){
  var BAD = /clarity\.ms\/tag\/[^?]+\?ref=gtm/i;

  function nuke(node){
    if (!node) return;
    if (node.tagName==='SCRIPT' && node.src && BAD.test(node.src)) { node.remove(); }
  }

  // Dọn các tag đã có sẵn
  document.querySelectorAll('script[src]').forEach(function(s){ if (BAD.test(s.src)) s.remove(); });

  // Quan sát mọi node mới thêm vào DOM
  new MutationObserver(function(muts){
    muts.forEach(function(m){ if (m.addedNodes) m.addedNodes.forEach(nuke); });
  }).observe(document.documentElement, {childList:true, subtree:true});

  // Chặn ngay từ lúc set src
  var _create = Document.prototype.createElement;
  Document.prototype.createElement = function(tag){
    var el = _create.call(this, tag);
    if (String(tag).toLowerCase()==='script'){
      var _set = el.setAttribute;
      el.setAttribute = function(name, value){
        if (name==='src' && BAD.test(value)) return; // block
        return _set.apply(this, arguments);
      };
      Object.defineProperty(el, 'src', {
        get(){ return this.getAttribute('src'); },
        set(v){ if (BAD.test(v)) return; this.setAttribute('src', v); }
      });
    }
    return el;
  };
})();
</script>
<?php }, 0);

/* 2) NẠP lại Clarity sạch (ID đúng) sau tương tác/idle */
add_action('wp_footer', function(){ ?>
<script>
(function(){
  if (window.__cleanClarityLoaded) return;

  function hasClean(){ return !!document.querySelector('script[src*="clarity.ms/tag/p132zuhmqp"]'); }
  function hasAnyClarity(){ return !!document.querySelector('script[src*="clarity.ms/tag/"]'); }

  function loadClean(){
    if (window.__cleanClarityLoaded) return;
    // nếu đã có thẻ clarity (do cache/CDN) thì thôi
    if (hasClean() || hasAnyClarity()) { window.__cleanClarityLoaded = true; return; }

    window.__cleanClarityLoaded = true;
    (function(c,l,a,r,i,t,y){
      c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
      t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/p132zuhmqp";
      y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script");
  }

  ['pointerdown','touchstart','mousemove','scroll','keydown'].forEach(function(ev){
    window.addEventListener(ev, loadClean, {once:true, passive:true});
  });
  if ('requestIdleCallback' in window) { requestIdleCallback(loadClean, {timeout:4000}); }
  else { setTimeout(loadClean, 4000); }
})();
</script>
<?php });
