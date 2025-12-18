/**
 * hanukkah.js
 * Menorah candles (subtle floating sparkles near top)
 */
(function(){
    window.kssRegisterEffect('hanukkah', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 18;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.5;
        var color = cfg.color || '#80d6ff';

        ts.load(id, {
            fullScreen: { enable: false },
            particles: {
                number: { value: density },
                color: { value: color },
                opacity: { value: { min: 0.7, max: 1 }, animation: { enable: true, speed: 0.7 } },
                size: { value: { min: 2, max: 5 } },
                move: { enable: true, direction: 'top', speed: speed, outModes: 'out' },
                shape: { type: 'circle' }
            },
            detectRetina: true
        });
        // occasional menorah popup from bottom
        try {
            var showMenorah = function(){
                var url = (window.kssImages && window.kssImages.menorah) ? window.kssImages.menorah : null;
                if ( ! url ) return;
                var img = document.createElement('img');
                img.src = url;
                img.className = 'kss-popup-large';
                img.style.left = Math.floor(Math.random()*60 + 20) + '%';
                img.style.width = '18vw';
                img.style.maxWidth = '360px';
                img.setAttribute('aria-hidden','true');
                document.body.appendChild(img);
                setTimeout(function(){ try { document.body.removeChild(img); } catch(e){} }, 2600);
            };
            setInterval(showMenorah, 9000 + Math.random()*7000);
        } catch(e) { /* ignore */ }
    });
})();
