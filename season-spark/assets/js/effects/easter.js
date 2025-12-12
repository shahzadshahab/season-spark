/**
 * easter.js
 * Eggs and bunnies particles (image if present) with gentle float
 */
(function(){
    window.kssRegisterEffect('easter', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 24;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.5;
        var color = cfg.color || '#f7c6d8';

        var images = [];
        if ( typeof kssImages !== 'undefined' ) {
            if ( kssImages.egg ) images.push({ src: kssImages.egg, width: 34, height: 34 });
            if ( kssImages.bunny ) images.push({ src: kssImages.bunny, width: 34, height: 34 });
        }

        ts.load(id, {
            fullScreen: { enable: false },
            particles: {
                number: { value: density },
                shape: { type: images.length ? 'image' : 'circle', image: images.length ? images : undefined },
                color: { value: color },
                opacity: { value: { min: 0.6, max: 0.95 } },
                size: { value: { min: 12, max: 28 } },
                move: { enable: true, direction: 'bottom', speed: speed, outModes: 'out' }
            },
            detectRetina: true
        });
    });
})();
