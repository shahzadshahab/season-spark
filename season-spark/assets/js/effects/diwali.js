/**
 * diwali.js
 * Small lights / lanterns / rangoli sparkles
 */
(function(){
    window.kssRegisterEffect('diwali', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 40;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.6;
        var color = cfg.color || '#ffd166';

        ts.load(id, {
            fullScreen: { enable: false },
            fpsLimit: 60,
            particles: {
                number: { value: density },
                color: { value: color },
                opacity: { value: { min: 0.6, max: 1 }, animation: { enable: true, speed: 1, minimumValue: 0.4 } },
                size: { value: { min: 1.5, max: 5 } },
                move: { enable: true, direction: 'none', speed: speed, random: true, outModes: 'out' },
                shape: { type: 'circle' }
            },
            detectRetina: true
        });
        // occasional large diya popup from bottom
        try {
            var showDiya = function(){
                var url = (window.kssImages && window.kssImages.diya) ? window.kssImages.diya : null;
                if ( ! url ) return;
                var img = document.createElement('img');
                img.src = url;
                img.className = 'kss-popup-large';
                img.style.left = Math.floor(Math.random()*60 + 20) + '%';
                img.style.width = '20vw';
                img.style.maxWidth = '360px';
                img.setAttribute('aria-hidden','true');
                document.body.appendChild(img);
                setTimeout(function(){ try { document.body.removeChild(img); } catch(e){} }, 2600);
            };
            setInterval(showDiya, 8000 + Math.random()*9000);
        } catch(e) { /* ignore */ }
    });
})();
