function RecaptchaV3() {
    var siteKey = document.getElementById("recaptchaKey").value;
    var badgePosition = document.getElementById("badgePosition").value;
    var clientId = grecaptcha.render('grecaptcha-box', {
        'sitekey': siteKey,
        'badge': badgePosition,
        'size': 'invisible'
    });

    grecaptcha.ready(function() {
        grecaptcha.execute(clientId, {
            action: 'submit'
        })
    });
}
