/**
 * christmas.js
 * Snowfall + subtle twinkles
 */
(function(){
    window.kssRegisterEffect('christmas', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 60;
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 0.9;
        var color = cfg.color || '#51D6FF';

        ts.load(id, {
            fullScreen: { enable: false },
            fpsLimit: 60,
            particles: {
                number: { value: density, density: { enable: false } },
                color: { value: color },
                opacity: { value: { min: 0.4, max: 0.9 }, animation: { enable: true, speed: 0.5, minimumValue: 0.2 } },
                size: { value: { min: 1, max: 4 } },
                move: { enable: true, direction: 'bottom', outModes: { default: 'out' }, speed: speed, random: false },
                shape: { type: 'circle' }
            },
            detectRetina: true,
            interactivity: { detectsOn: 'canvas', events: { onHover: { enable: false }, onClick: { enable: false } } }
        });
    });
})();
