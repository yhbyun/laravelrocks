/*globals window, $, _, Backbone, validator */
//yhbyun modified original source
(function () {
    'use strict';

    var Ghost = {};
    Ghost.init = function() {
        // Create a new editor
        Ghost.editor = new Ghost.Editor.Main();
    }

    window.Ghost = Ghost;
    window.addEventListener("load", Ghost.init, false);
}());
