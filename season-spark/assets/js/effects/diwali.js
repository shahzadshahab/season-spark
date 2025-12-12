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
    });
})();
