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
    });

})();
