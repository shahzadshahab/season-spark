/**
 * easter.js
 * Eggs and bunnies particles (image if present) with gentle float
 */
(function(){
    window.kssRegisterEffect('easter', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 24;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.5;
        var color = cfg.color || '#f7c6d8';

        // Prefer egg images for drop effect
        var images = [];
        if ( typeof kssImages !== 'undefined' && kssImages.egg ) {
            images.push({ src: kssImages.egg, width: 36, height: 36 });
        }

        ts.load(id, {
            fullScreen: { enable: false },
            particles: {
                number: { value: density },
                shape: { type: images.length ? 'image' : 'circle', image: images.length ? images : undefined },
                color: { value: color },
                opacity: { value: { min: 0.6, max: 0.95 } },
                size: { value: { min: 14, max: 30 } },
                move: { enable: true, direction: 'bottom', speed: speed, random: true, outModes: 'out' }
            },
            detectRetina: true
        });

        // occasional larger egg drop implemented with a DOM element for a playful 'drop & shake' illusion
        try {
            var showEgg = function(){
                var url = (window.kssImages && window.kssImages.egg) ? window.kssImages.egg : null;
                if ( ! url ) return;
                var img = document.createElement('img');
                img.src = url;
                img.className = 'kss-popup-large kss-egg-drop';
                img.style.left = Math.floor(Math.random()*70 + 10) + '%';
                img.style.width = '6vw';
                img.style.maxWidth = '120px';
                img.setAttribute('aria-hidden','true');
                document.body.appendChild(img);
                setTimeout(function(){ try { document.body.removeChild(img); } catch(e){} }, 2400);
            };
            setInterval(showEgg, 2800 + Math.random()*3600);
        } catch(e) { /* ignore */ }
    });
})();
