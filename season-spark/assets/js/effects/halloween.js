/**
 * halloween.js
 * Ghosts (image), pumpkins (image) with floating motion and fog (subtle)
 */
(function(){
    window.kssRegisterEffect('halloween', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 20;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.5;
        var color = cfg.color || '#ffffff';

        // Use simple particle shapes and images if available
        var images = [];
        if ( typeof kssImages !== 'undefined' ) {
            if ( kssImages.ghost ) images.push({ src: kssImages.ghost, width: 60, height: 60 });
            if ( kssImages.pumpkin ) images.push({ src: kssImages.pumpkin, width: 50, height: 50 });
        }

        var shapeType = images.length ? 'image' : 'circle';

        ts.load(id, {
            fullScreen: { enable: false },
            fpsLimit: 45,
            particles: {
                number: { value: density, density: { enable: false } },
                color: { value: color },
                opacity: { value: { min: 0.35, max: 0.9 }, random: true },
                size: { value: { min: 14, max: 36 } },
                move: { enable: true, direction: 'top', outModes: { default: 'out' }, speed: speed, random: true },
                shape: { type: shapeType, image: images.length ? images : undefined }
            },
            backgroundMask: { enable: false },
            detectRetina: true,
            interactivity: { events: { onHover: { enable: false }, onClick: { enable: false } } }
        });
    });
})();
