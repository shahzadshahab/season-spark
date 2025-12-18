/**
 * thanksgiving.js
 * Falling leaves + turkey silhouette (leaves preferred)
 */
(function(){
    window.kssRegisterEffect('thanksgiving', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 36;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.9;
        var color = cfg.color || '#b86b2b';

        var images = [];
        if ( typeof kssImages !== 'undefined' && kssImages.leaf ) {
            images.push({ src: kssImages.leaf, width: 28, height: 28 });
        }

        ts.load(id, {
            fullScreen: { enable: false },
            particles: {
                number: { value: density },
                shape: { type: images.length ? 'image' : 'circle', image: images.length ? images : undefined },
                color: { value: color },
                opacity: { value: { min: 0.5, max: 0.95 } },
                size: { value: { min: 8, max: 20 } },
                move: { enable: true, direction: 'bottom', speed: speed, random: true, outModes: 'out' }
            },
            detectRetina: true
        });
        // occasional big turkey popup from bottom
        try {
            var showTurkey = function(){
                var url = (window.kssImages && window.kssImages.turkey) ? window.kssImages.turkey : null;
                if ( ! url ) return;
                var img = document.createElement('img');
                img.src = url;
                img.className = 'kss-popup-large kss-popup-slow';
                img.style.left = Math.floor(Math.random()*60 + 20) + '%';
                img.style.width = '28vw';
                img.style.maxWidth = '420px';
                img.setAttribute('aria-hidden','true');
                document.body.appendChild(img);
                setTimeout(function(){ try { document.body.removeChild(img); } catch(e){} }, 2800);
            };
            setInterval(showTurkey, 7000 + Math.random()*8000);
        } catch(e) { /* ignore */ }
    });
})();
