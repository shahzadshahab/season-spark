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
    });
})();
