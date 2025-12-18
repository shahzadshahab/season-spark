/**
 * kss-public.js
 * Front-end initializer for Season Spark effects.
 * - Loads only if effects are enabled (assets enqueued conditionally)
 * - Exposes a safe registry and initializes effects that have DOM containers
 * - Respects prefers-reduced-motion and admin global reduce motion
 */

(function () {
    'use strict';

    // Safety guard
    if ( typeof window === 'undefined' ) return;

    var settings = window.kssSettings || {};
    var userDisabled = false;
    try {
        userDisabled = ( localStorage.getItem( 'kss_motion_disabled' ) === '1' );
    } catch ( e ) {
        userDisabled = false;
    }

    var prefersReduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
    var siteReduced = settings.global && settings.global.motion_reduced;
    var finalReduced = Boolean( prefersReduced || siteReduced || userDisabled );

    // small accessible toggle (already printed by PHP)
    var toggle = document.getElementById( 'kss-toggle-motion' );
    if ( toggle ) {
        toggle.addEventListener( 'click', function () {
            var pressed = toggle.getAttribute( 'aria-pressed' ) === 'true';
            pressed = ! pressed;
            toggle.setAttribute( 'aria-pressed', pressed ? 'true' : 'false' );
            try {
                localStorage.setItem( 'kss_motion_disabled', pressed ? '1' : '0' );
            } catch ( e ) { /* ignore */ }
            // re-initialize or reload page to apply. choose reload to keep code simple and deterministic.
            window.location.reload();
        });

        toggle.setAttribute( 'aria-pressed', finalReduced ? 'true' : 'false' );
        toggle.title = finalReduced ? 'Motion disabled (click to enable)' : 'Motion enabled (click to disable)';
    }

    if ( finalReduced ) {
        // Skip any heavy visual initialization
        return;
    }

    // Registry for effect init functions
    window.kssEffectRegistry = window.kssEffectRegistry || {};

    // Helper to register effect functions (used by per-effect files)
    window.kssRegisterEffect = function ( key, fn ) {
        if ( typeof key !== 'string' || typeof fn !== 'function' ) return;
        window.kssEffectRegistry[ key ] = fn;
    };

    // helper: run an effect if DOM container exists
    function initEffect(key) {
        var container = document.querySelector('.kss-effect-' + key);
        if (!container) return;
        var id = 'kss-particles-' + key;
        container.id = id;

        container.style.position = 'fixed';
        container.style.left = '0';
        container.style.top = '0';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.pointerEvents = 'none';
        container.style.zIndex = 99998;

        var fn = window.kssEffectRegistry[key];
        if ( window.tsParticles && typeof fn === 'function' ) {
            try {
                fn(window.tsParticles, id, (settings.effects && settings.effects[key]) ? settings.effects[key] : {});
            } catch ( e ) {
                // do not break the site — log for devs
                if ( window.console && console.error ) console.error('kss effect init error for', key, e);
            }
        } else {
            // tsParticles not loaded? will attempt to wait a bit (simple backoff)
            var attempts = 0;
            var waitForParticles = setInterval(function(){
                attempts++;
                if ( window.tsParticles && typeof fn === 'function' ) {
                    clearInterval(waitForParticles);
                    try {
                        fn(window.tsParticles, id, (settings.effects && settings.effects[key]) ? settings.effects[key] : {});
                    } catch(e){
                        if ( window.console && console.error ) console.error('kss effect init error for', key, e);
                    }
                }
                if ( attempts > 10 ) {
                    clearInterval(waitForParticles);
                }
            }, 300);
        }
    }

    // Initialize all effects that have containers (DOM printed by PHP)
    document.addEventListener('DOMContentLoaded', function () {
        var nodes = document.querySelectorAll('[data-kss-effect]');
        if (!nodes || nodes.length === 0) return;
        nodes.forEach(function (node) {
            var key = node.getAttribute('data-kss-effect');
            if ( key ) {
                initEffect(key);
            }
        });

        // If any effect requested a custom cursor, apply an overlay cursor (robust cross-browser)
        try {
            if ( settings && settings.effects ) {
                var anyCursor = false;
                Object.keys( settings.effects ).forEach(function(k){
                    var cfg = settings.effects[k] || {};
                    if ( cfg.custom_cursor ) {
                        anyCursor = true;
                    }
                });

                if ( anyCursor ) {
                    // map effect keys to image keys in kssImages
                    var effectToImage = {
                        christmas: 'snowflake',
                        halloween: 'ghost',
                        valentines: 'heart',
                        newyear: 'star',
                        easter: 'bunny',
                        thanksgiving: 'turkey',
                        independence: 'spark',
                        diwali: 'diya',
                        hanukkah: 'menorah',
                        generic: 'leaf'
                    };

                    var url = null;
                    Object.keys( settings.effects ).some(function(k){
                        var cfg = settings.effects[k] || {};
                        if ( cfg.custom_cursor ) {
                            // prefer per-effect custom image URL in settings if provided (generic/custom graphics)
                            if ( cfg.custom_cursor_image ) {
                                url = cfg.custom_cursor_image;
                                return true;
                            }
                            var imgKey = effectToImage[k] || 'cursor';
                            if ( window.kssImages && window.kssImages[ imgKey ] ) {
                                url = window.kssImages[ imgKey ];
                                return true; // stop
                            }
                        }
                        return false;
                    });
                    // fallback
                    if ( ! url ) {
                        url = (window.kssImages && window.kssImages.cursor) ? window.kssImages.cursor : null;
                    }
                    if ( url ) {
                        // create overlay element
                        var cursorEl = document.createElement('div');
                        cursorEl.id = 'kss-custom-cursor';
                        cursorEl.style.position = 'fixed';
                        cursorEl.style.left = '0';
                        cursorEl.style.top = '0';
                        cursorEl.style.width = '48px';
                        cursorEl.style.height = '48px';
                        cursorEl.style.backgroundImage = 'url(' + url + ')';
                        cursorEl.style.backgroundSize = 'contain';
                        cursorEl.style.backgroundRepeat = 'no-repeat';
                        cursorEl.style.backgroundPosition = 'center';
                        cursorEl.style.pointerEvents = 'none';
                        cursorEl.style.zIndex = 2147483647; // top
                        cursorEl.style.transform = 'translate3d(-9999px,-9999px,0)';
                        document.body.appendChild(cursorEl);

                        // hide native cursor
                        document.documentElement.classList.add('kss-custom-cursor');

                        var mouseX = 0, mouseY = 0, rafPending = false;
                        function onMove(e){
                            mouseX = e.clientX; mouseY = e.clientY;
                            if ( ! rafPending ) {
                                rafPending = true;
                                        requestAnimationFrame(function(){
                                            // Position overlay so the TOP of the image is the hotspot
                                            cursorEl.style.transform = 'translate3d(' + (mouseX) + 'px,' + (mouseY) + 'px,0)';
                                            rafPending = false;
                                        });
                            }
                        }

                        document.addEventListener('mousemove', onMove, { passive: true });
                        document.addEventListener('mouseenter', onMove, { passive: true });

                        // Clean up on unload
                        window.addEventListener('unload', function(){
                            try { document.removeEventListener('mousemove', onMove); } catch(e){}
                        });
                    }
                }
            }
        } catch ( e ) { /* ignore */ }
    });

})();
