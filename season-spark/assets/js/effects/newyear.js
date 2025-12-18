/**
 * newyear.js
 * Lightweight fireworks bursts on interval (not continuous heavy fireworks)
 * - Creates occasional bursts to keep load low
 */
(function(){
    window.kssRegisterEffect('newyear', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 0; // fireworks use emitters instead
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 2.5;
        var color = cfg.color || '#ffffff';
        var palette = ['#ff2d95','#ffd700','#51D6FF','#ff6b3a','#7bff6b','#9b5cff','#ffcb3a'];

        // Use particles.js-style emitters via tsParticles emitters plugin is heavier;
        // keep simple: small bursts triggered via setInterval
        ts.load(id, {
            fullScreen: { enable: false },
            fpsLimit: 60,
            particles: {
                number: { value: 0 },
                color: { value: palette },
                opacity: { value: 1, animation: { enable: true, speed: 1, minimumValue: 0 } },
                size: { value: { min: 3, max: 8 } },
                move: { enable: true, speed: speed, decay: 0.08, outModes: 'destroy' }
            },
            detectRetina: true
        }).then(function(container){
            // create light bursts occasionally
            try {
                var burst = function(){
                    if (!container) return;
                    var x = Math.random() * container.canvas.size.width;
                    var y = Math.random() * container.canvas.size.height * 0.5; // upper half
                    for (var i = 0; i < 28; i++) {
                        var col = palette[ Math.floor(Math.random()*palette.length) ];
                        container.particles.addParticle({
                            x: x,
                            y: y,
                            color: { value: col },
                            velocity: { horizontal: (Math.random()-0.5)*8, vertical: (Math.random()-0.5)*8 },
                            size: { value: Math.random()*4+2 },
                            opacity: { value: 1 },
                            life: { duration: { value: 1.6 } }
                        });
                    }
                };
                setInterval(burst, 2500 + Math.random()*3500);
            } catch(e){ if (window.console) console.error(e); }
        });
    });
})();
