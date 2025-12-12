/**
 * newyear.js
 * Lightweight fireworks bursts on interval (not continuous heavy fireworks)
 * - Creates occasional bursts to keep load low
 */
(function(){
    window.kssRegisterEffect('newyear', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 0; // fireworks use emitters instead
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 2.5;
        var color = cfg.color || '#ffd700';

        // Use particles.js-style emitters via tsParticles emitters plugin is heavier;
        // keep simple: small bursts triggered via setInterval
        ts.load(id, {
            fullScreen: { enable: false },
            fpsLimit: 60,
            particles: {
                number: { value: 0 },
                color: { value: color },
                opacity: { value: { min: 0.6, max: 1 } },
                size: { value: { min: 1, max: 3 } },
                move: { enable: true, speed: speed, decay: 0.05, outModes: 'destroy' }
            },
            detectRetina: true
        }).then(function(container){
            // create light bursts occasionally
            try {
                var burst = function(){
                    if (!container) return;
                    var x = Math.random() * container.canvas.size.width;
                    var y = Math.random() * container.canvas.size.height * 0.5; // upper half
                    for (var i = 0; i < 18; i++) {
                        container.particles.addParticle({
                            x: x,
                            y: y,
                            velocity: { horizontal: (Math.random()-0.5)*6, vertical: (Math.random()-0.5)*6 },
                            size: { value: Math.random()*2+0.5 },
                            opacity: { value: 1 }
                        });
                    }
                };
                setInterval(burst, 2500 + Math.random()*3500);
            } catch(e){ if (window.console) console.error(e); }
        });
    });
})();
