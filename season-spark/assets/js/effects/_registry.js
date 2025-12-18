/**
 * _registry.js
 * Small helper file included before per-effect scripts if desired.
 * It ensures window.kssRegisterEffect exists (kss-public also does this — this is defensive).
 */
(function(){
    if ( typeof window === 'undefined' ) return;
    window.kssEffectRegistry = window.kssEffectRegistry || {};
    window.kssRegisterEffect = window.kssRegisterEffect || function(key, fn){
        if ( typeof key !== 'string' || typeof fn !== 'function' ) return;
        window.kssEffectRegistry[key] = fn;
    };
})();
