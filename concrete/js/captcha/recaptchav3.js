function RecaptchaV3() {
    $(".recaptcha-v3").each(function () {
        var el = $(this);
        var clientId = grecaptcha.render($(el).attr("id"), {
            'sitekey': $(el).attr('data-sitekey'),
            'badge': $(el).attr('data-badge'),
            'theme': $(el).attr('data-theme'),
            'size': 'invisible',
        });
        grecaptcha.ready(function () {
            grecaptcha.execute(clientId, {
                action: 'submit'
            })
        });
    });
}
