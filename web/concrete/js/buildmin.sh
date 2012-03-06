find ccm_app/*.js -type f -exec cat {} \; -exec echo "" \; > ccm.app.js
cp ccm_app/__bootstrap.twipsy.js ./bootstrap.twipsy.js
cp ccm_app/_bootstrap.popover.js ./bootstrap.popover.js

/usr/bin/java -jar /Applications/yuicompressor-2.4.7/build/yuicompressor-2.4.7.jar -o ccm.app.min.js ccm.app.js
rm ccm.app.js
mv ccm.app.min.js ccm.app.js