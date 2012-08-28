find ccm_app/*.js -type f -exec cat {} \; -exec echo "" \; > ccm.app.js

/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o jquery.cookie.js ccm_app/jquery.cookie.js
/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o bootstrap.tooltip.js ccm_app/__bootstrap.tooltip.js
/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o bootstrap.transitions.js ccm_app/__bootstrap.transitions.js
/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o bootstrap.popover.js ccm_app/_bootstrap.popover.js 
/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o bootstrap.alert.js ccm_app/_bootstrap.alert.js 
/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o ccm.app.min.js ccm.app.js
rm ccm.app.js
mv ccm.app.min.js ccm.app.js