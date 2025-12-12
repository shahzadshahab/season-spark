/**
 * independence.js
 * Red/white/blue fireworks + small flags (if present)
 */
(function(){
    window.kssRegisterEffect('independence', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 0; // use bursts
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 2.6;
        var color = cfg.color || '#ffffff';

        ts.load(id, {
            fullScreen: { enable: false },
            particles: {
                number: { value: 0 },
                color: { value: ['#b22234','#ffffff','#3c3b6e'] },
                opacity: { value: 1 },
                size: { value: { min: 1, max: 3 } },
                move: { enable: true, speed: speed, outModes: 'destroy' }
            },
            detectRetina: true
        }).then(function(container){
            // periodic bursts
            var burst = function(){
                if (!container) return;
                var x = Math.random() * container.canvas.size.width;
                var y = Math.random() * container.canvas.size.height * 0.5;
                for (var i = 0; i < 20; i++) {
                    container.particles.addParticle({
                        x: x,
                        y: y,
                        velocity: { horizontal: (Math.random()-0.5)*7, vertical: (Math.random()-0.5)*7 },
                        size: { value: Math.random()*2+0.5 }
                    });
                }
            };
            setInterval(burst, 2200 + Math.random()*3000);
        });
    });
})();
