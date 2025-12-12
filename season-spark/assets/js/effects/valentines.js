/**
 * valentines.js
 * Floating hearts + small confetti shards
 */
(function(){
    window.kssRegisterEffect('valentines', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 36;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 1.0;
        var color = cfg.color || '#ff3b6b';

        var shapes = [];
        if ( typeof kssImages !== 'undefined' && kssImages.heart ) {
            shapes.push({ src: kssImages.heart, width: 20, height: 20 });
        }

        ts.load(id, {
            fullScreen: { enable: false },
            fpsLimit: 60,
            particles: {
                number: { value: density },
                shape: { type: shapes.length ? 'image' : 'polygon', image: shapes.length ? shapes : undefined, polygon: { sides: 6 } },
                color: { value: color },
                opacity: { value: { min: 0.6, max: 0.95 } },
                size: { value: { min: 6, max: 18 } },
                move: { enable: true, direction: 'bottom', random: false, speed: speed, outModes: 'out' }
            },
            detectRetina: true
        });
    });
})();
