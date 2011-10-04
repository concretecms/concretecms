find ccm_app/*.js -type f -exec cat {} \; -exec echo "" \; > ccm.app.js
cp ccm_app/_jquery.tooltip.js ./jquery.tooltip.js