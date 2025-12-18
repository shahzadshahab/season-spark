/**
 * independence.js
 * Red/white/blue fireworks + small flags (if present)
 */
(function(){
    window.kssRegisterEffect('independence', function(ts, id, cfg){
        var density = (cfg.density && cfg.density > 0) ? cfg.density : 0; // use bursts
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : 2.6;
        var color = cfg.color || '#ffffff';
        var palette = ['#b22234','#ffffff','#3c3b6e','#ffd700','#51D6FF'];

        ts.load(id, {
            fullScreen: { enable: false },
            particles: {
                number: { value: 0 },
                color: { value: palette },
                opacity: { value: 1, animation: { enable: true, speed: 1, minimumValue: 0 } },
                size: { value: { min: 3, max: 8 } },
                move: { enable: true, speed: speed, decay: 0.08, outModes: 'destroy' }
            },
            detectRetina: true
        }).then(function(container){
            // periodic bursts
            var burst = function(){
                if (!container) return;
                var x = Math.random() * container.canvas.size.width;
                var y = Math.random() * container.canvas.size.height * 0.5;
                for (var i = 0; i < 26; i++) {
                    var col = palette[ Math.floor(Math.random()*palette.length) ];
                    container.particles.addParticle({
                        x: x,
                        y: y,
                        color: { value: col },
                        velocity: { horizontal: (Math.random()-0.5)*9, vertical: (Math.random()-0.5)*9 },
                        size: { value: Math.random()*4+2 },
                        life: { duration: { value: 1.6 } }
                    });
                }
            };
            setInterval(burst, 2200 + Math.random()*3000);
        });
    });
})();
