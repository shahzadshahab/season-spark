/**
 * generic.js
 * Includes rain, stars, and bubbles — lightweight
 * The admin 'generic' effect may choose which sub-effect to run via cfg.mode (rain/stars/bubbles)
 */
(function(){
    window.kssRegisterEffect('generic', function(ts, id, cfg){
        var mode = (cfg.mode || 'rain'); // default rain
        var density = (cfg.density && cfg.density > 0) ? cfg.density : (mode === 'bubbles' ? 30 : 60);
        var speed = (cfg.speed && cfg.speed > 0) ? cfg.speed : (mode === 'bubbles' ? 0.8 : 1.2);
        var color = cfg.color || '#9bd3ff';

        if ( mode === 'bubbles' ) {
            ts.load(id, {
                fullScreen: { enable: false },
                particles: {
                    number: { value: density },
                    color: { value: color },
                    size: { value: { min: 4, max: 14 } },
                    move: { enable: true, direction: 'top', speed: speed, outModes: 'out' },
                    opacity: { value: { min: 0.4, max: 0.9 } }
                },
                detectRetina: true
            });
        } else if ( mode === 'stars' ) {
            ts.load(id, {
                fullScreen: { enable: false },
                particles: {
                    number: { value: density },
                    color: { value: '#fff' },
                    size: { value: { min: 0.6, max: 2 } },
                    move: { enable: true, direction: 'none', speed: 0.2 },
                    opacity: { value: { min: 0.6, max: 1 }, animation: { enable: true, speed: 0.5 } }
                },
                detectRetina: true
            });
        } else { // rain
            ts.load(id, {
                fullScreen: { enable: false },
                particles: {
                    number: { value: density },
                    color: { value: '#9bd3ff' },
                    size: { value: { min: 0.6, max: 1.8 } },
                    move: { enable: true, direction: 'bottom', speed: speed, outModes: 'out' },
                    opacity: { value: { min: 0.4, max: 0.8 } }
                },
                detectRetina: true
            });
        }
        // If this effect is used as 'Custom Graphics' and a custom_bg URL is provided,
        // create a lightweight animated background element instead of particles.
        try {
            if ( cfg && cfg.custom_bg ) {
                var el = document.getElementById(id);
                if ( el ) {
                    var bg = document.createElement('div');
                    bg.className = 'kss-generic-bg';
                    bg.style.position = 'fixed';
                    bg.style.left = '0';
                    bg.style.top = '0';
                    bg.style.width = '100%';
                    bg.style.height = '100%';
                    bg.style.zIndex = 99996;
                    bg.style.pointerEvents = 'none';
                    bg.style.backgroundImage = 'url(' + cfg.custom_bg + ')';
                    bg.style.backgroundRepeat = 'repeat';
                    bg.style.backgroundSize = cfg.bg_size || 'auto';
                    bg.style.opacity = cfg.bg_opacity || '0.55';
                    bg.style.transform = 'translate3d(0,0,0)';
                    document.body.appendChild(bg);
                    // subtle parallax animation
                    var t = 0;
                    var raf = function(){
                        t += 0.0006 * (cfg.speed || 1);
                        bg.style.backgroundPosition = Math.floor(t*100) + 'px ' + Math.floor(t*40) + 'px';
                        requestAnimationFrame(raf);
                    };
                    requestAnimationFrame(raf);
                }
            }
        } catch(e) { /* ignore */ }
    });
})();
