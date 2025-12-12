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
    });
})();
